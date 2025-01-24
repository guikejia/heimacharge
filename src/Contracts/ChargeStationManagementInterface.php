<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationInfo;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationQueueInfo;
use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\StationRangeSpace;

interface ChargeStationManagementInterface
{
    /**
     * 站点列表.
     */
    public function ListStations(int $page = 1, int $page_size = 10): array;

    /**
     * 获取站点信息.
     */
    public function GetStationInfo(int $station_id): StationInfo;

    /**
     * 站点车位列表.
     */
    public function ListStationSpaces(int $station_id): array;

    /**
     * 获取站点车位信息.
     */
    public function GetStationSpace(int $station_id, int $space_id): StationRangeSpace;
}
