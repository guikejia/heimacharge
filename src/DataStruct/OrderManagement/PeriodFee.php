<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\OrderManagement;

use DateTime;
use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class PeriodFee extends DataModel
{
    /**
     * 时段电费，单位：分
     * @var int
     */
    #[Map('charge_ccy')]
    public int $charge_ccy; // json:charge_ccy Required

    /**
     * 时段充电度数，单位：wh
     * @var int
     */
    #[Map('charge_power')]
    public int $charge_power; // json:charge_power Required

    /**
     * 时段电费单价，单位：分/kwh
     * @var int
     */
    #[Map('charge_unit_fee')]
    public int $charge_unit_fee; // json:charge_unit_fee Required

    /**
     * 时段结束时间
     * @var DateTime
     */
    #[Map('end_time')]
    public DateTime $end_time; // json:end_time Required

    /**
     * 时段服务费，单位：分
     * @var int
     */
    #[Map('service_ccy')]
    public int $service_ccy; // json:service_ccy Required

    /**
     * 时段服务费费单价，单位：分/kwh
     * @var int
     */
    #[Map('service_unit_fee')]
    public int $service_unit_fee; // json:service_unit_fee Required

    /**
     * 时段开始时间
     * @var DateTime
     */
    #[Map('start_time')]
    public DateTime $start_time; // json:start_time Required
}
