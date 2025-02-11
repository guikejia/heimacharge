<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\DeviceManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

/**
 * @property ConnectorStatusInfo[] $connector_status_infos
 */
class DeviceInfo extends DataModel
{
    /**
     * 设备名称.
     */
    #[Map('alias')]
    public ?string $alias;

    /**
     * 设备类型.
     */
    #[Map('category')]
    public ?int $category;

    /**
     * 设备接口信息.
     */
    #[Map('connector_status_infos', type: ConnectorStatusInfo::class)]
    public ?array $connector_status_infos;

    /**
     * 设备类型名称.
     */
    #[Map('device_type_name')]
    public ?string $device_type_name;

    /**
     * 设备id.
     */
    #[Map('id')]
    public int $id;

    /**
     * 设备最大电流
     */
    #[Map('max_current')]
    public ?float $max_current;

    /**
     * 设备最大功率.
     */
    #[Map('max_power')]
    public ?float $max_power;

    /**
     * 设备最大电压.
     */
    #[Map('max_voltage')]
    public ?float $max_voltage;

    /**
     * 设备最小电流
     */
    #[Map('min_current')]
    public ?float $min_current;

    /**
     * 设备最小功率.
     */
    #[Map('min_power')]
    public ?float $min_power;

    /**
     * 设备最小电压.
     */
    #[Map('min_voltage')]
    public ?float $min_voltage;

    /**
     * 设备额定电流
     */
    #[Map('rated_current')]
    public ?float $rated_current;

    /**
     * 设备额定功率.
     */
    #[Map('rated_power')]
    public ?float $rated_power;

    /**
     * 设备额定电压.
     */
    #[Map('rated_voltage')]
    public ?float $rated_voltage;

    /**
     * 站点 id.
     */
    #[Map('station_id')]
    public ?int $station_id;

    /**
     * 设备实时工作状态
     * 1 free.
     */
    #[Map('status')]
    public int $status;
}
