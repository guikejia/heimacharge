<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\SystemDataManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

/**
 * 车辆品牌下的车型
 */
class CarType extends DataModel
{
    /**
     * 车型id
     * @var int
     */
    #[Map('type_id')]
    public int $type_id;

    /**
     * 车型名称
     * @var string
     */
    #[Map('name')]
    public string $name;

    /**
     * 车型是否可用
     * @var int
     */
    #[Map('enable')]
    public int $enable;

    /**
     * 车型对应的品牌名
     * @var string
     */
    #[Map('brand_name')]
    public string $brand_name;
}
