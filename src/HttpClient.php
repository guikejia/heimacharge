<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\ChargeBusinessException;
use Guikejia\HeiMaCharge\Exceptions\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Hyperf\Guzzle\ClientFactory;
use Psr\Container\ContainerInterface;

class HttpClient
{
    public function __construct(
        protected ContainerInterface $container,
        protected Utils $utils,
        protected Config $config,
    ) {}

    /**
     * @throws ChargeBusinessException
     * @throws Exception
     */
    public function request(string $method, string $uri, array $options = [], int $try_count = 0): array
    {
        $_options = [
            'debug' => true,
            'base_uri' => $this->config->getBaseUri(),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if ($uri != '/v2/authorization/login') {
            if (! isset($options['Authorization'])) {
                $options['Authorization'] = $this->getAuthorToken();
            }
            if (isset($options['Authorization'])) {
                $_options['headers']['Authorization'] = 'Bearer ' . $options['Authorization'];
            }
        }

        $timestamp = (int) (microtime(true) * 1000);
        $nonce = $this->utils->getNonce(12);
        $exec_result = 'SUCCESS';
        $exec_msg = '';

        $request_data = ['method' => $method, 'uri' => $uri, 'options' => $options];

        $data = '';
        if (isset($options['body'])) {
            $json_body = json_encode($options['body']);
            $data = $this->utils->encryptedData($json_body, $nonce);
        }

        if (isset($options['query'])) {
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

            foreach ($options['query'] as $k => $v) {
                $_options['headers'][$k] = $v;
            }

            // $_options['headers']['data'] = $data;
        }

        try {
            $real_request_data = ['method' => $method, 'uri' => $uri, 'options' => $options];
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
        } catch (Exception|GuzzleException|RequestException $e) {
            $response_data = [];
            $real_response_data = $e->getMessage();
            $exec_result = 'FAIL';
            $exec_msg = $e->getMessage();
            if ($e instanceof RequestException) {
                var_dump('状态码:', $e->getCode());
            } elseif ($e instanceof GuzzleException) {
                $response_data = $e->getMessage();
            }

            $message = explode("\n", $e->getMessage());
            $error = json_decode($message[1], true);
            $error['error'] = $message[0];
            return $error;
        } finally {
            $this->hook(['uri' => $uri, 'request_data' => $request_data, 'real_request_data' => $real_request_data, $response_data, $real_response_data, $exec_result, $exec_msg]);
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

    public function getAuthorToken(): string
    {
        $author_key = 'black_horse_access_token';
        $access_token = redis()->get($author_key);
        if ($access_token) {
            return $access_token;
        }

        // 请求黑马原力接口获取token
        $re = $this->request(
            'POST',
            '/v2/authorization/login',
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
            redis()->set($author_key, $access_token, ['expires_in' => $expired_in - 60]);
            return $access_token;
        }
        throw new \Exception('获取黑马原力则获取token失败');
    }

    // 业务方可以通过切片的方式，在请求前后做一些操作(比如记录日志)
    public function hook(array $params): void {}

    protected function create(array $options = []): Client
    {
        return $this->container->get(ClientFactory::class)->create($options);
    }
}
