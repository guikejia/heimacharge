<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\DeviceManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class CreateDeviceData extends DataModel
{
    /**
     * 设备名称.
     */
    #[Map('name')]
    public string $name;

    /**
     * 设备类型id.
     */
    #[Map('device_type_id')]
    public int $device_type_id;

    /**
     * 设备的 VIN 码
     */
    #[Map('vin')]
    public int $vin;

    /**
     * 设备sn号.
     */
    #[Map('device_sn')]
    public int $device_sn;

    /**
     * 设备的充电接口类型.
     * 1 直流充电口
     * 2 交流充电口
     * 3 移动充电机器人.
     */
    #[Map('charger_type')]
    public string $charger_type;

    /**
     * 设备协议.
     */
    #[Map('protocol', type: DeviceProtocol::class)]
    public DeviceProtocol $protocol;

    /**
     * 设备状态 1:启用 2:停用.
     */
    #[Map('status')]
    public int $status;
}
