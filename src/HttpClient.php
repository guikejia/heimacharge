<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\ChargeBusinessException;
use Guikejia\HeiMaCharge\Exceptions\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Hyperf\Guzzle\ClientFactory;
use Psr\Container\ContainerInterface;

class HttpClient
{
    public const LOGIN_URI = '/v2/authorization/login';

    public const TRY_COUNT = 3;

    public function __construct(
        protected ContainerInterface $container,
        protected Utils $utils,
        protected Config $config,
    ) {}

    /**
     * @throws ChargeBusinessException
     * @throws Exception
     */
    public function request(string $method, string $uri, array $options = [], int $try_count = self::TRY_COUNT): array
    {
        if ($try_count <= 0) {
            throw new ChargeBusinessException(sprintf('请求共%s尝试,最终失败', self::TRY_COUNT));
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
            $status = $response->getStatusCode();

            if ($status != 200) {
                throw new Exception('请求失败');
            }

            // 接口原始还回值
            $real_response_data = $response->getBody()->getContents();
            // 接口原始返回值 json
            $ori_contents = json_decode($real_response_data, true);
            // 接口原始返回值 data 解密
            $contents = $this->utils->decryptedData($ori_contents['data'], $ori_contents['nonce']);
            // 业务返回值
            $response_data = json_decode($contents, true);

            // 校验黑马侧签名
            $re = $this->utils->verifySignatureWithBH($ori_contents['signature'], $ori_contents['data'], $uri, $ori_contents['timestamp'], $ori_contents['nonce'], $method);
            if (! $re) {
                throw new Exception('验签失败');
            }
            return $response_data;
        } catch (\Throwable $e) {
            $error_code = 0;
            $error_msg = $e->getMessage();
            $exec_result = 'FAIL';
            $exec_msg = $e->getMessage();
            $response_data = ['error_code' => $error_code, 'error_msg' => $error_msg];
            $real_response_data = $error_msg;
            if ($e instanceof ClientException) {
                $response = $e->getResponse();
                if ($response->getStatusCode() != 200) {
                    $body = $response->getBody();
                    $error_content = $body->getContents();
                    $error_content_arr = (array) json_decode($error_content);
                    if (isset($error_content_arr['code'])) {
                        $error_code = $error_content_arr['code'];
                        $error_msg = $error_content_arr['message'] ?? $e->getMessage();
                        $response_data = ['error_code' => $error_code, 'error_msg' => $error_msg];
                    }
                    // 若为非登录接口返回的错误状态码是 40001、40002、400023 则重新获取token
                    if ($uri != self::LOGIN_URI && in_array($error_code, [40001, 40002, 40003])) {
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
                'exec_count' => self::TRY_COUNT - $try_count,
            ]);
            // token失效需要重新登录
            if ($is_need_re_login) {
                $this->getAutoAuthorToken(true);
            }
            // 请求错误后重试
            if ($exec_result != 'SUCCESS') {
                return $this->request($method, $ori_url, $ori_options, $try_count);
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
        throw new ChargeBusinessException('获取黑马原力则获取token失败');
    }

    /**
     * 业务方可以通过切片的方式，在请求前后做一些操作(比如记录日志).
     */
    public function hook(array $params): void {}

    protected function create(array $options = []): Client
    {
        return $this->container->get(ClientFactory::class)->create($options);
    }
}
