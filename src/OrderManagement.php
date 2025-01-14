<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\OrderManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\ChargeStation;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderUser;

class OrderManagement implements OrderManagementInterface
{
    /**
     * @param string $merchant_order_no
     * @param OrderUser $user
     * @param ChargeStation|null $station
     * @return array
     */
    public function CreateNewOrder(string $merchant_order_no, OrderUser $user, ?ChargeStation $station): array
    {
        // TODO: Implement CreateNewOrder() method.
    }

    /**
     * @param string $merchant_order_no
     * @return mixed
     */
    public function FinishOrder(string $merchant_order_no): mixed
    {
        // TODO: Implement FinishOrder() method.
    }

    /**
     * @param string $merchant_order_no
     * @return OrderInfo
     */
    public function GetOrderInfo(string $merchant_order_no): OrderInfo
    {
        // TODO: Implement GetOrderInfo() method.
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
        // TODO: Implement ListOrders() method.
    }

    /**
     * @param string|null $start_date
     * @param string|null $end_date
     * @return mixed
     */
    public function OrderStatistics(?string $start_date, ?string $end_date): mixed
    {
        // TODO: Implement OrderStatistics() method.
    }
}