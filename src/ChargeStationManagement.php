<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\ChargeStationManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationInfo;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationRangeSpace;

class ChargeStationManagement implements ChargeStationManagementInterface
{
    public function __construct(
        protected HttpClientFactory $http,
    ) {
    }

    /**
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function ListStations(int $page = 1, int $page_size = 10): array
    {
        $stations = $this->http->get('/stations/list', [
            'page_no' => $page,
            'page_size' => $page_size,
        ]);

        return [
            'page_no' => $stations['page_no'],
            'page_size' => $stations['page_size'],
            'total_count' => $stations['total_count'],
            'total_page' => $stations['total_page'],
            'results' => array_map(function($result) {
                return new StationInfo($result);
            }, $stations['results']),
        ];
    }

    /**
     * @param int $station_id
     * @return StationInfo
     */
    public function GetStationInfo(int $station_id): StationInfo
    {
        $station = $this->http->get('/stations/' . $station_id);

        return new StationInfo($station);
    }

    /**
     * @param int $station_id
     * @return array
     */
    public function ListStationSpaces(int $station_id): array
    {
        $spaces = $this->http->get('/stations/' . $station_id . '/spaces/list');

        return [
            'page_no' => $spaces['page_no'],
            'page_size' => $spaces['page_size'],
            'total_count' => $spaces['total_count'],
            'total_page' => $spaces['total_page'],
            'results' => array_map(function($result) {
                return new StationRangeSpace($result);
            }, $spaces['results']),
        ];
    }

    /**
     * @param int $station_id
     * @param int $space_id
     * @return StationRangeSpace
     */
    public function GetStationSpace(int $station_id, int $space_id): StationRangeSpace
    {
        $space = $this->http->get('/stations/' . $station_id . '/spaces/' . $space_id);

        return new StationRangeSpace($space);
    }
}
