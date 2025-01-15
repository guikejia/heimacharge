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
    ) {
    }

    /**
     * @throws ChargeBusinessException
     * @throws Exception
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        $_options = [
            'debug' => true,
            'base_uri' => $this->config->getBaseUri(),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if (isset($options['Authorization'])) {
            $_options['headers']['Authorization'] = $options['Authorization'];
        }

        $timestamp = round(microtime(true) * 1000);
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

        $body = [
            'client_id' => $this->config->getClientId(),
            'data' => $data,
            'nonce' => $nonce,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];
        $_options['body'] = json_encode($body);
        var_dump($_options['body']);

        try {
            $response = $this->create()->request($method, $uri, $_options);

            $status = $response->getStatusCode();
            $contents = $response->getBody()->getContents();

            return $contents ? json_decode($contents, true) : [];
        } catch (GuzzleException $e) {
            var_dump($e->getMessage());
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

    protected function create(array $options = []): Client
    {
        return $this->container->get(ClientFactory::class)->create($options);
    }
}
