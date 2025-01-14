<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\ChargeBusinessException;
use Guikejia\HeiMaCharge\Exceptions\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Guzzle\ClientFactory;
use Psr\Container\ContainerInterface;
use Random\RandomException;

class HttpClientFactory
{
    public function __construct(
        protected ContainerInterface $container,
        protected Authentication  $authentication,
        protected Config $config,
    ) {}

    protected function create(array $options = []): Client
    {
        return $this->container->get(ClientFactory::class)->create($options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array
     * @throws ChargeBusinessException
     * @throws GuzzleException
     * @throws RandomException
     * @throws Exception
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        $_options = [
            'base_uri' => $this->config->getBaseUri(),
            'headers' => ['Accept' => 'application/json'],
        ];

        $data = '';
        if (isset($options['json'])) {
            $json = json_encode($options['json']);
            $data = $this->authentication->encryptedData($json);
        }

        if (isset($options['query'])) {
            $query = http_build_query($options['query']);
            $uri = $uri.'?'.$query;
        }

        $timestamp = time();
        $nonce = $this->authentication->getNonce();
        $signature = $this->authentication->genSignature($method, $uri, $timestamp, $nonce, $data);

        $body = [
            'data' => $data,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $signature,
            'client_id' => $this->config->getClientId(),
        ];
        $_options['json'] = json_encode($body);

        $response = $this->create()->request($method, $uri, $_options);
        $contents = $response->getBody()->getContents();

        $status = $response->getStatusCode();

        return [
            'code' => $status,
            'data' => $contents ? json_decode($contents, true) : null,
        ];
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
            'json' => $body,
        ];
        return $this->request(method: 'POST', uri: $uri, options: $options);
    }

    public function put($uri, array $options = []): array
    {
        return $this->request(method: 'PUT', uri: $uri, options: $options);
    }

    public function delete($uri): array
    {
        return $this->request(method: 'DELETE', uri: $uri);
    }
}
