<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\OrderManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\ChargeStation;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderQueueInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderUser;

class OrderManagement implements OrderManagementInterface
{
    public function __construct(
        protected HttpClient $http,
    ) {
    }

    /**
     * @param string $merchant_order_no
     * @param OrderUser $user
     * @param ChargeStation|null $station
     * @return array
     */
    public function CreateNewOrder(string $merchant_order_no, OrderUser $user, ?ChargeStation $station): array
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
            'queue_info' => new OrderQueueInfo($orders['queue_info']),
        ];
    }

    /**
     * @param string $merchant_order_no
     * @return mixed
     */
    public function FinishOrder(string $merchant_order_no): mixed
    {
        return $this->http->delete('/v2/orders/' . $merchant_order_no);
    }

    /**
     * @param string $merchant_order_no
     * @return OrderInfo
     */
    public function GetOrderInfo(string $merchant_order_no): OrderInfo
    {
        $order = $this->http->get('/v2/orders/' . $merchant_order_no);

        return new OrderInfo($order);
    }

    /**
     * @param string $start_date
     * @param string $end_date
     * @param int $page_no
     * @param int $page_size
     * @return array
     */
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
            'results' => array_map(fn($item) => new OrderInfo($item), $orders['results']),
        ];
    }

    /**
     * @param string|null $start_date
     * @param string|null $end_date
     * @return mixed
     */
    public function OrderStatistics(?string $start_date, ?string $end_date): mixed
    {
        return $this->http->get('/v2/orders/statistics', [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }
}
