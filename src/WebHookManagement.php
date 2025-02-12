<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\ChargeBusinessException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WebHookManagement
{
    // 订单状态变更事件
    public const OrderStatusChangeEvent = 1;

    // 订单信息事件
    public const OrderInfoEvent = 2;

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

        if (! isset($params['event_type'])) {
            throw new ChargeBusinessException('event_type is invalid');
        }

        if (! in_array($params['event_type'], [self::OrderStatusChangeEvent, self::OrderInfoEvent])) {
            throw new ChargeBusinessException('event_type is invalid.');
        }

        if (empty($params['data'])) {
            throw new ChargeBusinessException('data is invalid');
        }

        if (empty($params['nonce'])) {
            throw new ChargeBusinessException('nonce is invalid');
        }
        if (! isset($params['timestamp'])) {
            throw new ChargeBusinessException('timestamp is invalid');
        }

        if (! isset($params['signature'])) {
            throw new ChargeBusinessException('signature is invalid');
        }

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
