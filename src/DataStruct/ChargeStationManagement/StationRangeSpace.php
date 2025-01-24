<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class StationRangeSpace extends DataModel
{
    /**
     * 车位 id
     * @var int
     */
    #[Map('id')]
    public int $id;

    /**
     * 停车位编码
     * @var string
     */
    #[Map('space_no')]
    public string $space_no;

    /**
     * 站点 id
     * @var int
     */
    #[Map('station_id')]
    public int $station_id;

    /**
     * 站点区域 id
     * @var int
     */
    #[Map('range_id')]
    public int $range_id;

    /**
     * 停车位名称
     * @var string
     */
    #[Map('name')]
    public string $name;

    /**
     * 停车位是否可用
     * @var bool
     */
    #[Map('enable')]
    public bool $enable;

    /**
     * 区域排队信息
     * @var RangeQueueInfo
     */
    #[Map('queue_info')]
    public ?RangeQueueInfo $queue_info;
}
