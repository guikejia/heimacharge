<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\ChargeBusinessException;
use Guikejia\HeiMaCharge\Exceptions\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
            'debug' => false,
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
                $_options['headers']['Authorization'] = $options['Authorization'];
            }
        }

        $timestamp = (int) (microtime(true) * 1000);
        $nonce = $this->utils->getNonce(12);
        $data = '';

        if (isset($options['body'])) {
            $json_body = json_encode($options['body']);
            $data = $this->utils->encryptedData($json_body, $nonce);
        }

        if (isset($options['query'])) {
            $query = http_build_query($options['query']);
            $uri = $uri . '?' . $query;
        }

        $request_data = $this->utils->getSignatureData($method, $uri, $timestamp, $nonce, $data);

        $signature = $this->utils->genSignature($request_data);

        if ($method === 'POST') {
            $body = [
                'client_id' => $this->config->getClientId(),
                'data' => $data,
                'nonce' => $nonce,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ];
            $_options['json'] = $body;
        } elseif ($method === 'GET') {
            $_options['query'] = array_merge([
                'client_id' => $this->config->getClientId(),
                'data' => $data,
                'nonce' => $nonce,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ], $options['query']);
        }

        try {
            $response = $this->create()->request($method, $uri, $_options);
            $status = $response->getStatusCode();
            var_dump($status);
            // exit;

            $ori0_contents = $response->getBody()->getContents();

            var_dump($ori0_contents);
            // /exit;

            $ori_contents = json_decode($ori0_contents, true);
            $contents = $this->utils->decryptedData($ori_contents['data'], $ori_contents['nonce']);
            $contents = json_decode($contents, true);

            // 校验黑马侧签名
            $re = $this->utils->verifySignatureWithBH($ori_contents['signature'], $ori_contents['data'], $uri, $ori_contents['timestamp'], $ori_contents['nonce'], $method);
            if (! $re) {
                throw new Exception('验签失败');
            }
            return $contents;
        } catch (GuzzleException $e) {
            $message = explode("\n", $e->getMessage());
            $error = json_decode($message[1], true);
            $error['error'] = $message[0];
            return $error;
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

    protected function create(array $options = []): Client
    {
        return $this->container->get(ClientFactory::class)->create($options);
    }
}
