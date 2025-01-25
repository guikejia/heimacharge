<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

/**
 * @property ChargeFeeDetail[] $charge_fee_details
 */
class CreateStationData extends DataModel
{
    /**
     * 站点名称.
     */
    #[Map('name')]
    public string $name;

    /**
     * 站点引导说明.
     */
    #[Map('description')]
    public ?string $description;

    /**
     * 站点地址
     */
    #[Map('address')]
    public string $address;

    /**
     * 站点城市.
     */
    #[Map('city')]
    public string $city;

    /**
     * 站点服务电话.
     */
    #[Map('service_tel')]
    public string $service_tel;

    /**
     * 站点站点状态
     * 1 待运营
     * 2 运营中
     * 3 暂停运营
     * 4 已关闭.
     */
    #[Map('status')]
    public int $status;

    /**
     * 站点车位类型
     * 1 标准车位
     * 2 单元门牌号
     * 3 标准停车区域
     * 4 固定停车区域
     */
    #[Map('park_type')]
    public int $park_type;

    /**
     * 站点数量.
     */
    #[Map('park_num')]
    public int $park_num;

    #[Map('operate_start_date')]
    public string $operate_start_date;

    #[Map('operate_end_date')]
    public string $operate_end_date;

    #[Map('latitude')]
    public float $latitude;

    #[Map('longitude')]
    public float $longitude;

    /**
     * 站点免停时间.
     */
    #[Map('park_fee_free', type: ParkFeeFree::class)]
    public ParkFeeFree $park_fee_free;

    /**
     * 快充时段费率.
     */
    #[Map('fast_charge_fee_details', type: ChargeFeeDetail::class)]
    public ChargeFeeDetail $fast_charge_fee_details;

    /**
     * 慢充时段费率.
     */
    #[Map('slow_charge_fee_details', type: ChargeFeeDetail::class)]
    public ChargeFeeDetail $slow_charge_fee_details;

    /**
     * 超充时段费率.
     */
    #[Map('super_charge_fee_details', type: ChargeFeeDetail::class)]
    public ChargeFeeDetail $super_charge_fee_details;

    /**
     * 站点 Logo 图片的 Base64 编码
     */
    #[Map('logos')]
    public ?array $logos;
}
