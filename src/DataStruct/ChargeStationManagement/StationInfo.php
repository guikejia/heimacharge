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
     * 站点 id.
     */
    #[Map('id')]
    public ?int $id;

    /**
     * 站点名称.
     */
    #[Map('name')]
    public string $name;

    /**
     * 站点地址
     */
    #[Map('address')]
    public string $address;

    /**
     * 站点联系电话.
     */
    #[Map('service_tel')]
    public ?string $service_tel;

    /**
     * 站点纬度（WGS84标准）.
     */
    #[Map('latitude')]
    public string $latitude;

    /**
     * 站点经度（WGS84标准）.
     */
    #[Map('longitude')]
    public string $longitude;

    /**
     * 1 等待运营
     * 2 运营中
     * 3 暂停运营
     * 4 已关闭.
     */
    #[Map('status')]
    public int $status;

    /**
     * 站点时段费用详情.
     */
    #[Map('fast_charge_fee_details', type: ChargeFeeDetail::class)]
    public ?array $fast_charge_fee_details;

    #[Map('slow_charge_fee_details', type: ChargeFeeDetail::class)]
    public ?array $slow_charge_fee_details;

    #[Map('super_charge_fee_details', type: ChargeFeeDetail::class)]
    public ?array $super_charge_fee_details;

    #[Map('logos')]
    public ?array $logos;
}
