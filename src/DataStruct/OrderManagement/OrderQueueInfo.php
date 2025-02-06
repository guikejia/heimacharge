<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\OrderManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class OrderQueueInfo extends DataModel
{
    /**
     * 订单期望等待时间.
     */
    #[Map('expected_wait_time')]
    public ?int $expected_wait_time;

    /**
     * 排队位置，若大于0，则说明该订单在排队中.
     */
    #[Map('position')]
    public ?int $position;

    /**
     * 订单已等待时间.
     */
    #[Map('wait_time')]
    public ?int $wait_time;
}
