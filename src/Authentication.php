<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\AuthenticationInterface;

class Authentication implements AuthenticationInterface
{
    public function __construct(
        protected HttpClient $http,
        protected Config     $config,
    ) {
    }

    public function AcquireAccessToken(): array
    {
        return $this->http->post('/v2/authorization/login', [
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
        ]);
    }
}
