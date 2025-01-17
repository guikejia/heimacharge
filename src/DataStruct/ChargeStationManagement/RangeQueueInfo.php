<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

/**
 * 区域排队信息.
 */
class RangeQueueInfo extends DataModel
{
    /**
     * 该区域排队功能是否开启.
     */
    #[Map('enable')]
    public ?bool $enable;

    /**
     * 该区域最新的排队位置.
     */
    #[Map('queue_position')]
    public ?int $queue_position;

    /**
     * 该区域排队功能是否可用.
     */
    #[Map('is_queue_available')]
    public ?bool $is_queue_available;
}
