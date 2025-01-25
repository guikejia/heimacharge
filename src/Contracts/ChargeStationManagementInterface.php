<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

use Guikejia\HeiMaCharge\DataStruct\ChargeStationManagement\CreateStationData;
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

    /**
     * 新建站点.
     */
    public function CreateStation(CreateStationData $data): array;

    /**
     * 站点车位
     * @param int $station_id
     * @param int $range_id
     * @param string $space_no
     * @param string $space_code
     * @return array
     */
    public function CreateStationSpace(int $station_id, int $range_id, string $space_no, string $space_code): array;

    /**
     * 批量新建站点
     * @param int $station_id
     * @param int $range_id
     * @param array $batch
     * @return array
     */
    public function BatchCreateStationSpaces(int $station_id, int $range_id, array $batch): array;


    /**
     * 创建一个站点区域
     * @param int $station_id 站点 ID
     * @param string $name 区域名称
     * @param int $range_type 区域类型  1:自动驾驶区域 2:非自动驾驶区域
     * @param string $description 区域描述
     * @return array
     */
    public function CreateStationRange(int $station_id, string $name, int $range_type, string $description): array;
}
