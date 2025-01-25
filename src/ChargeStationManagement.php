<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\ChargeStationManagementInterface;
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
}
