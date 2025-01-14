<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationInfo;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationRangeSpace;

interface ChargeStationManagementInterface
{
    /**
     * 站点列表
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function ListStations(int $page = 1, int $page_size = 10): array;

    /**
     * 获取站点信息
     * @param int $station_id
     * @return StationInfo
     */
    public function GetStationInfo(int $station_id): StationInfo;

    /**
     * 站点车位列表
     * @param int $station_id
     * @return array
     */
    public function ListStationSpaces(int $station_id): array;

    /**
     * 获取站点车位信息
     * @param int $station_id
     * @param int $space_id
     * @return StationRangeSpace
     */
    public function GetStationSpace(int $station_id, int $space_id): StationRangeSpace;
}
