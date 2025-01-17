<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\OrderManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class OrderQueueInfo extends DataModel
{
    /**
     * 订单期望等待时间
     * @var int
     */
    #[Map('expected_wait_time')]
    private ?int $expected_wait_time;

    /**
     * 排队位置，若大于0，则说明该订单在排队中
     * @var int
     */
    #[Map('position')]
    private ?int $position;

    /**
     * 订单已等待时间
     * @var int
     */
    #[Map('wait_time')]
    private ?int $wait_time;
}
