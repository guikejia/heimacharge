<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\ChargeBusinessException;
use Guikejia\HeiMaCharge\Exceptions\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Hyperf\Guzzle\ClientFactory;
use Psr\Container\ContainerInterface;

class HttpClient
{
    public const LOGIN_URI = '/v2/authorization/login';

    // 接口请求失败重试次数
    public const TRY_COUNT = 3;

    // 若为非登录接口返回的错误状态码是 40001、40002、400023 则重新获取token
    public const ERROR_CODES_NEED_RE_LOGIN = [40001, 40002, 40003];

    public const WebHookEventMap = [
        1 => 'OrderStatusChangeEvent',
        2 => 'OrderInfoEvent',
    ];

    public function __construct(
        protected ContainerInterface $container,
        protected Utils $utils,
        protected Config $config,
    ) {}

    /**
     * @throws ChargeBusinessException
     * @throws Exception
     */
    public function request(string $method, string $uri, array $options = [], int $try_count = self::TRY_COUNT, string $last_msg = ''): array
    {
        if ($try_count <= 0) {
            $last_msg = $last_msg ?: sprintf('%s请求共%s尝试,最终失败', $uri, self::TRY_COUNT);
            throw new ChargeBusinessException($last_msg);
        }
        --$try_count;
        $ori_url = $uri;
        $ori_options = $options;
        $http_config = $this->config->getHttpConfig();
        $_options = [
            'debug' => $http_config['debug'],
            'base_uri' => $this->config->getBaseUri(),
            'connect_timeout' => $http_config['connect_timeout'],
            'timeout' => $http_config['timeout'],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if ($uri != self::LOGIN_URI) {
            if (! isset($options['Authorization'])) {
                $options['Authorization'] = $this->getAutoAuthorToken();
            }
            if (isset($options['Authorization'])) {
                $_options['headers']['Authorization'] = 'Bearer ' . $options['Authorization'];
            }
        }

        $timestamp = (int) (microtime(true) * 1000);
        $nonce = $this->utils->getNonce(12);
        $exec_result = 'SUCCESS';
        $exec_msg = '';
        $is_need_re_login = false;

        $request_data = ['method' => $method, 'uri' => $uri, 'options' => $options];

        $data = '';
        if (isset($options['body'])) {
            $json_body = json_encode($options['body']);
            $data = $this->utils->encryptedData($json_body, $nonce);
        }

        if (isset($options['query']) && $options['query']) {
            ksort($options['query']);
            $query = http_build_query($options['query']);
            $uri = $uri . '?' . $query;
        }

        $request_data_with_sign_data = $this->utils->getSignatureData($method, $uri, $timestamp, $nonce, $data);
        $signature = $this->utils->genSignature($request_data_with_sign_data);

        if (in_array($method, ['POST', 'PATCH', 'PUT'])) {
            $body = [
                'client_id' => $this->config->getClientId(),
                'data' => $data,
                'nonce' => $nonce,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ];
            $_options['json'] = $body;
        } else {
            $_options['query'] = $options['query'];
            $_options['headers']['x-nonce'] = $nonce;
            $_options['headers']['x-signature'] = $signature;
            $_options['headers']['x-timestamp'] = $timestamp;
            $_options['headers']['x-client-id'] = $this->config->getClientId();

            // header头拼接业务参数
            foreach ($options['query'] as $k => $v) {
                $_options['headers'][$k] = $v;
            }
        }

        try {
            $real_request_data = ['method' => $method, 'uri' => $uri, 'options' => $_options];
            $response = $this->create()->request($method, $uri, $_options);
            $http_status_code = $response->getStatusCode();

            if ($http_status_code != 200) {
                throw new Exception('请求失败');
            }

            // 接口原始还回值
            $real_response_data = $response->getBody()->getContents();
            if (empty($real_response_data)) {
                throw new ChargeBusinessException('黑马原力侧接口返回值有为空!');
            }
            // 接口原始返回值 json
            $ori_contents = json_decode($real_response_data, true);
            if (! is_array($ori_contents)) {
                throw new ChargeBusinessException('黑马原力侧接口返回值有误');
            }
            // 黑马原力侧接口返回解为数组需要有 data,nonce,signature,timestamp
            if (! isset($ori_contents['data'])) {
                throw new ChargeBusinessException('黑马原力侧接口返回值有误[data]');
            }
            if (! isset($ori_contents['nonce'])) {
                throw new ChargeBusinessException('黑马原力侧接口返回值有误[nonce]');
            }
            if (! isset($ori_contents['signature'])) {
                throw new ChargeBusinessException('黑马原力侧接口返回值有误[signature]');
            }
            if (! isset($ori_contents['timestamp'])) {
                throw new ChargeBusinessException('黑马原力侧接口返回值有误[timestamp]');
            }

            // 接口原始返回值 data 解密
            $contents = $this->utils->decryptedData($ori_contents['data'], $ori_contents['nonce']);

            // 校验黑马侧签名
            $re = $this->utils->verifySignatureWithBlackHorse($ori_contents['signature'], $ori_contents['data'], $uri, $ori_contents['timestamp'], $ori_contents['nonce'], $method);
            if (! $re) {
                throw new ChargeBusinessException('验签失败');
            }

            // 业务返回值
            $response_data = json_decode($contents, true);

            if (! is_array($response_data)) {
                throw new ChargeBusinessException('黑马原力侧接口返回值有误');
            }

            return $response_data;
        } catch (\Throwable $e) {
            $error_code = 0;
            $error_msg = $e->getMessage();
            $exec_result = 'FAIL';
            $exec_msg = $e->getMessage();
            $response_data = ['error_code' => $error_code, 'error_msg' => $error_msg];
            $real_response_data = $real_response_data ?? $error_msg;
            $http_status_code = $http_status_code ?? 0;

            if ($e instanceof ServerException || $e instanceof ClientException) {
                $response = $e->getResponse();
                $http_status_code = $response->getStatusCode();
                if ($http_status_code != 200) {
                    $body = $response->getBody();
                    $error_content = $body->getContents();
                    $error_content_arr = (array) json_decode($error_content);
                    if (isset($error_content_arr['code'])) {
                        $error_code = $error_content_arr['code'];
                        $error_msg = $error_content_arr['message'] ?? $e->getMessage();
                        $response_data = ['error_code' => $error_code, 'error_msg' => $error_msg];
                    }
                    // 若为非登录接口返回的错误状态码是 40001、40002、40003 则重新获取token
                    if ($uri != self::LOGIN_URI && in_array($error_code, self::ERROR_CODES_NEED_RE_LOGIN)) {
                        $is_need_re_login = true;
                    }
                    throw new ChargeBusinessException($error_msg, $error_code);
                }
            }
            throw new ChargeBusinessException($error_msg, $error_code);
        } finally {
            $this->hook([
                'uri' => $uri,
                'method' => $method,
                'request_data' => $request_data,
                'real_request_data' => $real_request_data,
                'response_data' => $response_data,
                'real_response_data' => $real_response_data,
                'exec_result' => $exec_result,
                'exec_msg' => $exec_msg,
                'http_status_code' => $http_status_code,
                'exec_count' => self::TRY_COUNT - $try_count,
            ]);
            // token失效需要重新登录
            if ($is_need_re_login) {
                $this->getAutoAuthorToken(true);
            }
            // 请求错误后重试
            if ($exec_result != 'SUCCESS') {
                return $this->request($method, $ori_url, $ori_options, $try_count, $error_msg ?? $exec_msg);
            }
        }
    }

    public function get($uri, array $params = []): array
    {
        $options = [
            'query' => $params,
        ];
        return $this->request(method: 'GET', uri: $uri, options: $options);
    }

    public function post($uri, array $body = []): array
    {
        $options = [
            'body' => $body,
        ];
        return $this->request(method: 'POST', uri: $uri, options: $options);
    }

    public function put($uri, array $options = []): array
    {
        return $this->request(method: 'PUT', uri: $uri, options: $options);
    }

    public function patch($uri, array $options = []): array
    {
        return $this->request(method: 'PATCH', uri: $uri, options: $options);
    }

    public function delete($uri): mixed
    {
        return $this->request(method: 'DELETE', uri: $uri);
    }

    /**
     * 请求业务接口,主动获取黑马原图接口的凭证信息.
     * 注 业务方 可以提供 getTokenGetWithBlackHorse(string $key) 与 setTokenGetWithBlackHorse(string $key, string $value, int $expired_in) 两个方法来设置凭证缓存.
     * @param bool $force_refresh 是否强制刷新缓存凭证
     * @return string 返回凭证信息
     * @throws ChargeBusinessException
     * @throws Exception
     */
    public function getAutoAuthorToken(bool $force_refresh = false): string
    {
        $author_key = 'black_horse_access_token';

        if (! $force_refresh) {
            if (function_exists('getTokenGetWithBlackHorse')) {
                $access_token = getTokenGetWithBlackHorse($author_key);
                if ($access_token) {
                    return $access_token;
                }
            }
        }

        // 请求黑马原力接口获取token
        $re = $this->request(
            'POST',
            self::LOGIN_URI,
            [
                'body' => [
                    'client_id' => $this->config->getClientId(),
                    'client_secret' => $this->config->getClientSecret(),
                ],
            ]
        );
        $access_token = $re['access_token'] ?? '';
        $expired_in = (int) ($re['expired_in'] ?? '');
        if ($access_token && $expired_in) {
            function_exists('setTokenGetWithBlackHorse') && setTokenGetWithBlackHorse($author_key, $access_token, $expired_in);
            return $access_token;
        }
        throw new ChargeBusinessException('获取黑马原力侧token失败');
    }

    /**
     * 业务方可以通过切片的方式，在请求前后做一些操作(比如记录日志).
     */
    public function hook(array $params): void {}

    public function getWebHookData(array $params): array
    {
        // 订单事件
        $event_type = (int) ($params['event_type'] ?? 0);
        // 经过Base64编码的加密数据域
        $data = (string) ($params['data'] ?? '');
        // 随机12位字符，用于加密数据
        $nonce = (string) ($params['nonce'] ?? '');
        // 请求发送的时间戳，单位：毫秒
        $timestamp = (int) ($params['timestamp'] ?? 0);
        // 通过数字签名计算出的签名值经过 base64 编码后的字符串
        $signature = (string) ($params['signature'] ?? '');

        $contents = $this->utils->decryptedData($data, $nonce);
        // 业务返回值
        $decryption_data = json_decode($contents, true);

        // 校验黑马侧签名
        $re = $this->utils->verifySignatureWithBlackHorse($signature, $data, $this->config->getWebhookRequestPath(), $timestamp, $nonce, 'POST');

        $this->hook([
            'uri' => sprintf('/webhook/%s', self::WebHookEventMap[$event_type] ?? 'unknown[' . $event_type . ']'),
            'method' => 'POST',
            'request_data' => $decryption_data,
            'real_request_data' => $params,
            'response_data' => [],
            'real_response_data' => [],
            'exec_result' => $re ? 'SUCCESS' : '验签失败',
            'exec_msg' => $re ? 'SUCCESS' : 'FAIL',
            'http_status_code' => 200,
            'exec_count' => 1,
        ]);
        if (! $re) {
            throw new ChargeBusinessException('验签失败');
        }

        return ['event_type' => $event_type, 'data' => $decryption_data];
    }

    protected function create(array $options = []): Client
    {
        return $this->container->get(ClientFactory::class)->create($options);
    }
}
