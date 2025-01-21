<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\ChargeBusinessException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WebHookManagement
{
    public function __construct(
        protected HttpClient $http,
    ) {}

    /**
     * @throws ChargeBusinessException
     */
    public function callback(null|array|ServerRequestInterface $contents = null, ?array $params = null): array
    {
        if ($contents instanceof ServerRequestInterface) {
            $params = $contents->getParsedBody();
        }
        $params = (array) $params;
        return $this->http->getWebHookData($params);
    }

    /**
     * 成功响应.
     */
    public function success(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200)
            ->json([
                'success' => 1,
            ]);
    }

    /**
     * 失败响应.
     */
    public function fail(ResponseInterface $response, string $error_msg): ResponseInterface
    {
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(400)
            ->json([
                'success' => 0,
                'error_msg' => $error_msg,
            ]);
    }
}
