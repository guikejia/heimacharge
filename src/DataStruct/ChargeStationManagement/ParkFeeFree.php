<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

/**
 * 站点免停时间.
 */
class ParkFeeFree extends DataModel
{
    /**
     * 是否全时段免停，默认 false.
     */
    #[Map('is_all_day_free')]
    public bool $is_all_day_free = false;

    /**
     * 免费时长，单位：分钟.
     */
    #[Map('park_free_duration')]
    public int $park_free_duration;
}
