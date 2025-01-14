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
     * 设备名称
     * @var string
     */
    #[Map('alias')]
    public string $alias;

    /**
     * 设备类型
     * @var int
     */
    #[Map('category')]
    public int $category;

    /**
     * 设备接口信息
     * @var array
     */
    #[Map('connector_status_infos')]
    public array $connector_status_infos;

    /**
     * 设备类型名称
     * @var string
     */
    #[Map('device_type_name')]
    public string $device_type_name;

    /**
     * 设备id
     * @var int
     */
    #[Map('id')]
    public int $id;

    /**
     * 设备最大电流
     * @var float|null
     */
    #[Map('max_current')]
    public ?float $max_current;

    /**
     * 设备最大功率
     * @var float|null
     */
    #[Map('max_power')]
    public ?float $max_power;

    /**
     * 设备最大电压
     * @var float|null
     */
    #[Map('max_voltage')]
    public ?float $max_voltage;

    /**
     * 设备最小电流
     * @var float|null
     */
    #[Map('min_current')]
    public ?float $min_current;

    /**
     * 设备最小功率
     * @var float|null
     */
    #[Map('min_power')]
    public ?float $min_power;

    /**
     * 设备最小电压
     * @var float|null
     */
    #[Map('min_voltage')]
    public ?float $min_voltage;

    /**
     * 设备额定电流
     * @var float
     */
    #[Map('rated_current')]
    public float $rated_current;

    /**
     * 设备额定功率
     * @var float
     */
    #[Map('rated_power')]
    public float $rated_power;

    /**
     * 设备额定电压
     * @var float
     */
    #[Map('rated_voltage')]
    public float $rated_voltage;

    /**
     * 站点 id
     * @var int|null
     */
    #[Map('station_id')]
    public ?int $station_id;

    /**
     * 设备实时工作状态
     * 1 free
     * @var int
     */
    #[Map('status')]
    public int $status;
}
