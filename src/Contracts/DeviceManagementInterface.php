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
}
