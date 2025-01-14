<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Contracts\SystemDataManagementInterface;
use Guikejia\HeiMaCharge\DataStruct\SystemDataManagement\CarBrandTree;
use Guikejia\HeiMaCharge\DataStruct\SystemDataManagement\CarType;
use Guikejia\HeiMaCharge\Exceptions\Exception;

class SystemDataManagement implements SystemDataManagementInterface
{
    /**
     * 1: 车辆品牌列表
     */
    public const CAR_MODEL = 1;

    /**
     * 2: 车型列表
     */
    public const CAR_TYPE = 2;

    public function __construct(
        protected HttpClient $http,
    ) {
    }

    /**
     * @param int $type
     * @param int $page
     * @param int $page_size
     * @return array
     * @throws Exception
     */
    public function GetSysData(int $type, int $page = 1, int $page_size = 10): array
    {
        $data = $this->http->get('/v2/sysdata', [
            'page_no' => $page,
            'page_size' => $page_size,
            'type' => $type,
        ]);

        if ($type === self::CAR_MODEL) {
            return array_map(function ($item) {
                return new CarBrandTree($item);
            }, $data);
        } elseif ($type === self::CAR_TYPE) {
            return array_map(function ($item) {
                return new CarType($item);
            }, $data);
        } else {
            throw new Exception('非法的类型');
        }
    }
}
