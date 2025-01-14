<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\DeviceManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\DeviceManagement\DeviceInfo;

class DeviceManagement implements DeviceManagementInterface
{
    public function __construct(
        protected HttpClient $http,
    ) {
    }

    /**
     * @param int|null $station_id
     * @param string|null $real_status
     * @return array
     */
    public function ListDevices(?int $station_id, ?string $real_status): array
    {
        $devices = $this->http->get('/v2/devices/list', [
            'station_id' => $station_id,
            'real_status' => $real_status,
        ]);

        return array_map(function ($device) {
            return new DeviceInfo($device);
        }, $devices);
    }

    /**
     * @param int $device_id
     * @param string|null $real_status
     * @return DeviceInfo
     */
    public function GetDeviceInfo(int $device_id, ?string $real_status): DeviceInfo
    {
        $device = $this->http->get('/v2/devices/' . $device_id, [
            'real_status' => $real_status,
        ]);

        return new DeviceInfo($device);
    }
}
