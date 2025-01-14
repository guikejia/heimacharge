<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class ChargeFeeDetail extends DataModel
{
    /**
     * 时段开始时间，格式：hh:mm
     * @var string
     */
    #[Map('start_time')]
    public string $start_time;

    /**
     * 时段结束时间，格式：hh:mm
     * @var string
     */
    #[Map('end_time')]
    public string $end_time;

    /**
     * 时段电费，单位：0.01/kwn
     * @var int
     */
    #[Map('charge_fee')]
    public int $charge_fee;

    /**
     * 时段服务费，单位：0.01/kwn
     * @var int
     */
    #[Map('service_fee')]
    public int $service_fee;
}
