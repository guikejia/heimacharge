<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\OrderManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class OrderInfo extends DataModel
{
    /**
     * 用户车型id
     * @var int|null
     */
    #[Map('car_type_id')]
    public ?int $car_type_id;

    /**
     * 电费，单位：分
     * @var int
     */
    #[Map('charge_ccy')]
    public int $charge_ccy;

    /**
     * 结束充电时间
     * @var string|null
     */
    #[Map('charge_end_time')]
    public ?string $charge_end_time;

    /**
     * 总充电度数，单位：wh
     * @var int
     */
    #[Map('charge_power')]
    public int $charge_power;

    /**
     * 开始充电时间
     * @var string
     */
    #[Map('charge_start_time')]
    public string $charge_start_time;

    /**
     * 该订单绑定的设备id
     * @var int|null
     */
    #[Map('device_id')]
    public ?int $device_id; // json:device_id Optional

    /**
     * 商户订单号
     * @var string
     */
    #[Map('merchant_order_no')]
    public string $merchant_order_no;

    /**
     * RxLight内部订单号
     * @var string
     */
    #[Map('order_no')]
    public string $order_no;

    /**
     * 金额明细 @todo
     * @var PeriodFee|null
     */
    #[Map('period_fees')]
    private ?PeriodFee $period_fees;

    /**
     * 用户手机号
     * @var string
     */
    #[Map('phone')]
    public string $phone;

    /**
     * 用户车牌号
     * @var string
     */
    #[Map('plate_no')]
    public string $plate_no;

    /**
     * 排队信息（只针对软件付费用户开放）
     * @var OrderQueueInfo|null
     */
    #[Map('queue_info')]
    public ?OrderQueueInfo $queue_info;

    /**
     * 服务费，单位：分
     * @var int
     */
    #[Map('service_ccy')]
    public int $service_ccy;

    /**
     * 车位号
     * @var string
     */
    #[Map('space_no')]
    public string $space_no;

    /**
     * 站点id
     * @var int
     */
    #[Map('station_id')]
    public int $station_id;

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
     * 51 EXCEPTION
     * @var int
     */
    #[Map('status')]
    public int $status;

    /**
     * 总费用，单位：分
     * @var int
     */
    #[Map('total_ccy')]
    public int $total_ccy;
}
