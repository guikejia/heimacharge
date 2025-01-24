<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\OrderManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationQueueInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\ChargeStation;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderQueueInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderUser;

class OrderManagement implements OrderManagementInterface
{
    public function __construct(
        protected HttpClient $http,
    ) {}

    public function CreateNewOrder(string $merchant_order_no, OrderUser $user, ?ChargeStation $station = null): array
    {
        $orders = $this->http->post('/v2/orders/create', [
            'merchant_order_no' => $merchant_order_no,
            'phone_no' => $user->phone,
            'plate_no' => $user->plate_no,
            'station_id' => $user->station_id,
            'space_no' => $user->space_no,
            'space_id' => $user->space_id,
            'charge_basic_type' => $station->charge_basic_type,
            'charge_basic_value' => $station->charge_basic_value,
            'car_model_id' => $station->car_model_id,
        ]);

        return [
            'order_no' => $orders['order_no'],
            'queue_info' => new OrderQueueInfo($orders['queue_info'] ?? []),
        ];
    }

    public function FinishOrder(string $merchant_order_no): mixed
    {
        return $this->http->delete('/v2/orders/' . $merchant_order_no);
    }

    public function GetOrderInfo(string $merchant_order_no): OrderInfo
    {
        $order = $this->http->get('/v2/orders/' . $merchant_order_no);
        return new OrderInfo($order);
    }

    public function ListOrders(string $start_date, string $end_date, int $page_no = 1, int $page_size = 10): array
    {
        $orders = $this->http->get('/v2/orders/list', [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'page_no' => $page_no,
            'page_size' => $page_size,
        ]);

        return [
            'page_no' => $orders['page_no'],
            'page_size' => $orders['page_size'],
            'total_count' => $orders['total_count'],
            'total_page' => $orders['total_page'],
            'results' => array_map(fn ($item) => new OrderInfo($item), $orders['results'] ?? []),
        ];
    }

    public function OrderStatistics(?string $start_date, ?string $end_date): mixed
    {
        return $this->http->get('/v2/orders/statistics', [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function GetQueueInfo(int $station_id, string $space_no): StationQueueInfo
    {
        $queue_info = $this->http->get('/v2/orders/queue_info/' . $station_id . '/' . $space_no);
        return new StationQueueInfo($queue_info);
    }
}
