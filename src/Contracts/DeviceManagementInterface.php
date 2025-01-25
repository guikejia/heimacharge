<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

use Guikejia\HeiMaCharge\DataStruct\DeviceManagement\CreateDeviceData;
use Guikejia\HeiMaCharge\DataStruct\DeviceManagement\DeviceInfo;

interface DeviceManagementInterface
{
    /**
     * 设备列表.
     */
    public function ListDevices(?int $station_id, ?string $real_status): array;

    /**
     * 获取设备信息.
     */
    public function GetDeviceInfo(int $device_id, ?string $real_status): DeviceInfo;

    /**
     * 创建设备.
     */
    public function CreateDevice(CreateDeviceData $data): array;

    /**
     * 批量将设备绑定在某个站点和区域上.
     */
    public function BatchBindDevice(int $station_id, int $range_id, array $device_ids): array;

    /**
     * 删除设备.
     */
    public function DeleteDevice(int $device_id): array;

    /**
     * 批量解绑设备.
     */
    public function BatchUnBindDevice(array $device_ids): array;
}
