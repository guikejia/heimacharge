<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

/**
 * @property ChargeFeeDetail[] $charge_fee_details
 */
class StationInfo extends DataModel
{
    /**
     * 站点 id
     * @var int
     */
    #[Map('id')]
    public readonly int $id;

    /**
     * 站点名称
     * @var string
     */
    #[Map('name')]
    public string $name;

    /**
     * 站点地址
     * @var string
     */
    #[Map('address')]
    public string $address;

    /**
     * 站点联系电话
     * @var string|null
     */
    #[Map('service_tel')]
    public ?string $service_tel;

    /**
     * 站点纬度（WGS84标准）
     * @var string
     */
    #[Map('latitude')]
    public string $latitude;

    /**
     * 站点经度（WGS84标准）
     * @var string
     */
    #[Map('longitude')]
    public string $longitude;

    /**
     * 1 等待运营
     * 2 运营中
     * 3 暂停运营
     * 4 已关闭
     * @var int
     */
    #[Map('status')]
    public int $status;

    /**
     * 站点时段费用详情
     * @var array
     */
    #[Map('charge_fee_details')]
    public array $charge_fee_details;
}
