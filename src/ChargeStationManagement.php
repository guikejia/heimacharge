<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\ChargeStationManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\CreateStationData;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationInfo;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationRangeSpace;

class ChargeStationManagement implements ChargeStationManagementInterface
{
    public function __construct(
        protected HttpClient $http,
    ) {}

    public function ListStations(int $page = 1, int $page_size = 10): array
    {
        $stations = $this->http->get('/v2/stations/list', [
            'page_no' => $page,
            'page_size' => $page_size,
        ]);

        return [
            'page_no' => $stations['page_no'],
            'page_size' => $stations['page_size'],
            'total_count' => $stations['total_count'],
            'total_page' => $stations['total_page'],
            'results' => array_map(function ($result) {
                return new StationInfo($result);
            }, (array) ($stations['results'] ?? [])),
        ];
    }

    public function GetStationInfo(int $station_id): StationInfo
    {
        $station = $this->http->get('/v2/stations/' . $station_id);
        return new StationInfo($station);
    }

    public function ListStationSpaces(int $station_id): array
    {
        $spaces = $this->http->get('/v2/stations/' . $station_id . '/spaces/list');

        return [
            'page_no' => $spaces['page_no'],
            'page_size' => $spaces['page_size'],
            'total_count' => $spaces['total_count'],
            'total_page' => $spaces['total_page'],
            'results' => array_map(function ($result) {
                return new StationRangeSpace($result);
            }, (array) ($spaces['results'] ?? [])),
        ];
    }

    public function GetStationSpace(int $station_id, int $space_id): StationRangeSpace
    {
        $space = $this->http->get('/v2/stations/' . $station_id . '/spaces/' . $space_id);

        return new StationRangeSpace($space);
    }

    public function CreateStation(CreateStationData $data): array
    {
        $stations = $this->http->post('/v2/stations', [
            'name' => $data->name,
            'description' => $data->description,
            'address' => $data->address,
            'city' => $data->city,
            'service_tel' => $data->service_tel,
            'status' => $data->status,
            'park_type' => $data->park_type,
            'park_num' => $data->park_num,
            'operate_start_date' => $data->operate_start_date,
            'operate_end_date' => $data->operate_end_date,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'park_fee_free' => $data->park_fee_free,
            'fast_charge_fee_details' => $data->fast_charge_fee_details,
            'slow_charge_fee_details' => $data->slow_charge_fee_details,
            'super_charge_fee_details' => $data->super_charge_fee_details,
            'logos' => $data->logos,
        ]);

        return [
            'id' => $stations['id'],
        ];
    }

    /**
     * @param int $station_id 站点 ID
     * @param int $range_id 区域 ID
     * @param string $space_no 车位号
     * @param string $space_code 车位编码
     */
    public function CreateStationSpace(int $station_id, int $range_id, string $space_no, string $space_code): array
    {
        $space = $this->http->post('/v2/stations/' . $station_id . '/' . $range_id . '/spaces', [
            'space_no' => $space_no,
            'space_code' => $space_code,
        ]);
        return [
            'id' => $space['id'],
        ];
    }

    public function BatchCreateStationSpaces(int $station_id, int $range_id, array $batch): array
    {
        return $this->http->post('/v2/stations/' . $station_id . '/' . $range_id . '/spaces/batch', $batch);
    }

    /**
     * 创建一个站点区域
     * @param int $station_id 站点 ID
     * @param string $name 区域名称
     * @param int $range_type 区域类型  1:自动驾驶区域 2:非自动驾驶区域
     * @param string $description 区域描述
     */
    public function CreateStationRange(int $station_id, string $name, int $range_type, string $description): array
    {
        return $this->http->post('/v2/stations/' . $station_id . '/range', [
            'name' => $name,
            'range_type' => $range_type,
            'description' => $description,
        ]);
    }

    /**
     * 删除站点.
     */
    public function DeleteStation(int $station_id): array
    {
        return $this->http->delete('/v2/stations/' . $station_id);
    }

    /**
     * 获取站点区域列表.
     * @param int $station_id
     * @return array
     */
    public function ListStationRange(int $station_id): array
    {
        return $this->http->get('/v2/stations/' . $station_id . '/range/list');
    }
}
