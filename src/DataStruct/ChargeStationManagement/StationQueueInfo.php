<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class StationQueueInfo extends DataModel
{
    /**
     * 车位 id.
     */
    #[Map('position')]
    public int $position;

    /**
     * 状态码编码
     * 枚举值 - 名称
     * 1 - 有可用设备
     * 2 - 无可用设备且无法排队
     * 3 - 无可用设备，可排队
     */
    #[Map('code')]
    public string $code;

    /**
     * 站点 id.
     */
    #[Map('wait_time')]
    public int $wait_time;
}
