<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\OrderManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class OrderUser extends DataModel
{
    /**
     * 用户下单时的手机号
     * @var string
     */
    #[Map('phone')]
    public string $phone;

    /**
     * 用户下单时的车牌号
     * @var string
     */
    #[Map('plate_no')]
    public string $plate_no;

    /**
     * 用户下单时所在的站点id
     * @var int
     */
    #[Map('station_id')]
    public int $station_id;

    /**
     * 用户下单时所在的车位号
     * @var string
     */
    #[Map('space_no')]
    public string $space_no;

    /**
     * (可选)用户下单时所在的车位id
     * @var int|null
     */
    #[Map('space_id')]
    public ?int $space_id;
}
