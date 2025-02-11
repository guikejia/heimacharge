<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\DeviceManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\DeviceManagement\CreateDeviceData;
use Guikejia\HeiMaCharge\DataStruct\DeviceManagement\DeviceInfo;

class DeviceManagement implements DeviceManagementInterface
{
    public function __construct(
        protected HttpClient $http,
    ) {}

    public function ListDevices(?int $station_id, ?string $real_status): array
    {
        $devices = $this->http->get('/v2/devices/list', [
            'station_id' => $station_id,
            'real_status' => $real_status,
        ]);

        return array_map(function ($device) {
            return new DeviceInfo($device);
        }, (array) ($devices['results'] ?? []));
    }

    public function GetDeviceInfo(int $device_id, ?string $real_status): DeviceInfo
    {
        $device = $this->http->get('/v2/devices/' . $device_id, [
            'real_status' => $real_status,
        ]);

        return new DeviceInfo($device);
    }

    /**
     * 创建新设备.
     */
    public function CreateDevice(CreateDeviceData $data): array
    {
        $device = $this->http->post('/v2/devices', [
            'name' => $data->name,
            'device_type_id' => $data->device_type_id,
            'device_sn' => $data->device_sn,
            'vin' => $data->vin,
            'charger_type' => $data->charger_type,
            'protocol' => $data->protocol,
            'status' => $data->status,
        ]);
        return ['id' => $device['id']];
    }

    /**
     * 批量将设备绑定在某个站点和区域上.
     */
    public function BatchBindDevice(int $station_id, int $range_id, array $device_ids): array
    {
        $device = $this->http->patch('/v2/devices/bind', [
            'station_id' => $station_id,
            'range_id' => $range_id,
            'device_ids' => $device_ids,
        ]);
        return ['id' => $device['id']];
    }

    /**
     * 删除设备.
     */
    public function DeleteDevice(int $device_id): array
    {
        return $this->http->delete('/v2/devices/' . $device_id);
    }

    /**
     * 批量解绑设备.
     * @param mixed $device_ids
     */
    public function BatchUnBindDevice($device_ids): array
    {
        return $this->http->patch('/v2/unbind', [
            'device_ids' => $device_ids,
        ]);
    }
}
