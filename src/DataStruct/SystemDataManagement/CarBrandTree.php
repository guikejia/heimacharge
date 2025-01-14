<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\SystemDataManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

/**
 * 车辆品牌
 */
class CarBrandTree extends DataModel
{
    /**
     * 车辆品牌id
     * @var int
     */
    #[Map('brand_id')]
    public int $brand_id;

    /**
     * 车辆品牌名称
     * @var string
     */
    #[Map('name')]
    public string $name;

    /**
     * 车辆品牌状态
     * @var int
     */
    #[Map('status')]
    public int $status;

    /**
     * 车辆品牌icon下载地址
     * @var string
     */
    #[Map('icon')]
    public string $icon;

    /**
     * 车辆品牌下的车型列表
     * @var array
     */
    #[Map('children')]
    public array $children;
}
