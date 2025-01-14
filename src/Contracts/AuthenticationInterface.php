<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

interface AuthenticationInterface
{
    public function AcquireAccessToken(): array;
}
