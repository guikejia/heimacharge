<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationQueueInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\ChargeStation;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderInfo;
use Guikejia\HeiMaCharge\DataStruct\OrderManagement\OrderUser;

interface OrderManagementInterface
{
    /**
     * 下单
     * @param string $merchant_order_no
     * @param OrderUser $user
     * @param ChargeStation|null $station
     * @return array(
     *     'order_no',
     *      QueueInfo
     * )
     */
    public function CreateNewOrder(string $merchant_order_no, OrderUser $user, ?ChargeStation $station): array;

    /**
     * 结束订单
     * @param string $merchant_order_no
     * @return mixed
     */
    public function FinishOrder(string $merchant_order_no): mixed;

    /**
     * 获取订单信息
     * @param string $merchant_order_no
     * @return OrderInfo
     */
    public function GetOrderInfo(string $merchant_order_no): OrderInfo;

    /**
     * 订单列表
     * @param string $start_date
     * @param string $end_date
     * @param int $page_no
     * @param int $page_size
     * @return array(
     *      page_no
     *      page_size
     *      total_count
     *      total_page
     *      results: OrderInfo[]
     * )
     */
    public function ListOrders(string $start_date, string $end_date, int $page_no = 1, int $page_size = 10): array;

    /**
     * 订单统计
     * @param string|null $start_date
     * @param string|null $end_date
     * @return mixed
     */
    public function OrderStatistics(?string $start_date, ?string $end_date): mixed;

    /**
     * 获取站点车位排队信息.
     */
    public function GetQueueInfo(int $station_id, int $range_id): StationQueueInfo;
}
