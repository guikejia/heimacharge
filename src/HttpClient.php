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
        protected Utils              $utils,
        protected Config             $config,
    ) {}

    protected function create(array $options = []): Client
    {
        return $this->container->get(ClientFactory::class)->create($options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ?array
     * @throws ChargeBusinessException
     * @throws Exception
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        $_options = [
            'debug' => true,
            'base_uri' => $this->config->getBaseUri(),
            'headers' => ['Accept' => 'application/json'],
        ];

        $data = '';
        if (isset($options['body'])) {
            $json_body = json_encode($options['body']);
            $data = $this->utils->encryptedData($json_body);
        }

        if (isset($options['query'])) {
            $query = http_build_query($options['query']);
            $uri = $uri.'?'.$query;
        }

        $timestamp = time();
        $nonce = $this->utils->getNonce();
        $signature = $this->utils->genSignature($method, $uri, $timestamp, $nonce, $data);

        $body = [
            'data' => $data,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $signature,
            'client_id' => $this->config->getClientId(),
        ];
        $_options['body'] = json_encode($body);

        try {
            $response = $this->create()->request($method, $uri, $_options);
            $contents = $response->getBody()->getContents();

            $status = $response->getStatusCode();

            if ($status == 200) {
                return $contents ? json_decode($contents, true) : [];
            }

            return $contents ? json_decode($contents, true) : [];
        } catch (GuzzleException $e) {
            var_dump($e->getMessage());
            return [];
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
}
