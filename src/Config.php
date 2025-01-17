<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class Config
{
    protected array $config;

    public function __construct(
        protected ContainerInterface $container,
    ) {
        $this->config = $this->container->get(ConfigInterface::class)->get('black_horse_charge');
    }

    public function getHttpConfig(): array
    {
        return (array) ($this->config['http'] ?? []);
    }

    public function getBaseUri()
    {
        return $this->config['base_uri'];
    }

    public function getClientSecret(): string
    {
        return $this->config['client_secret'];
    }

    public function getClientId(): string
    {
        return $this->config['client_id'];
    }

    public function getHeiMaPublicKey(): string
    {
        return $this->config['heima_public_key'];
    }

    public function getPublicKey(): string
    {
        return $this->config['public_key'];
    }

    public function getPrivateKey(): string
    {
        return $this->config['private_key'];
    }

    public function getConfig()
    {
        return $this->config;
    }
}
