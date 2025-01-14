<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

use Guikejia\HeiMaCharge\DataStruct\DeviceManagement\DeviceInfo;

interface DeviceManagementInterface
{
    /**
     * 设备列表
     * @param int|null $station_id
     * @param string|null $real_status
     * @return array
     */
    public function ListDevices(?int $station_id, ?string $real_status): array;

    /**
     * 获取设备信息
     * @param int $device_id
     * @param string|null $real_status
     * @return DeviceInfo
     */
    public function GetDeviceInfo(int $device_id, ?string $real_status): DeviceInfo;
}
