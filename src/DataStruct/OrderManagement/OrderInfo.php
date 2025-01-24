<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\OrderManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class OrderInfo extends DataModel
{
    /**
     * 用户车型id.
     */
    #[Map('car_type_id')]
    public ?int $car_type_id;

    /**
     * 电费，单位：分.
     */
    #[Map('charge_ccy')]
    public int $charge_ccy;

    /**
     * 结束充电时间.
     */
    #[Map('charge_end_time')]
    public ?string $charge_end_time;

    /**
     * 总充电度数，单位：wh.
     */
    #[Map('charge_power')]
    public int $charge_power;

    /**
     * 开始充电时间.
     */
    #[Map('charge_start_time')]
    public string $charge_start_time;

    /**
     * 该订单绑定的设备id.
     */
    #[Map('device_id')]
    public ?int $device_id; // json:device_id Optional

    /**
     * 商户订单号.
     */
    #[Map('merchant_order_no')]
    public ?string $merchant_order_no;

    /**
     * RxLight内部订单号.
     */
    #[Map('order_no')]
    public ?string $order_no;

    /**
     * 用户手机号.
     */
    #[Map('phone_no')]
    public ?string $phone_no;

    /**
     * 用户车牌号.
     */
    #[Map('plate_no')]
    public ?string $plate_no;

    /**
     * 排队信息（只针对软件付费用户开放）.
     */
    #[Map('queue_info')]
    public ?OrderQueueInfo $queue_info;

    /**
     * 服务费，单位：分.
     */
    #[Map('service_ccy')]
    public ?int $service_ccy;

    /**
     * 车位号.
     */
    #[Map('space_no')]
    public ?string $space_no;

    /**
     * 站点id.
     */
    #[Map('station_id')]
    public ?int $station_id;

    /**
     * 订单状态
     * 0 CREATED
     * 1 PAID (ONLY FOR PREMIUM CUSTOMER)
     * 2 WAIT_DISPATCHING
     * 3 LINE_UP (ONLY FOR PREMIUM CUSTOMER)
     * 4 WAIT_MPS_READY
     * 5 CHARGING
     * 6 WAITING_FINISH
     * 7 WAITING_PAYMENT (ONLY FOR PREMIUM CUSTOMER)
     * 49 CANCELLED
     * 50 FINISHED
     * 51 EXCEPTION.
     */
    #[Map('status')]
    public ?int $status;

    /**
     * 总费用，单位：分.
     */
    #[Map('total_ccy')]
    public ?int $total_ccy;

    /**
     * 金额明细 @todo.
     */
    #[Map('period_fees')]
    private ?PeriodFee $period_fees;
}
