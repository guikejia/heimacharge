<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Contracts;

/**
 * 1. Client can get some business data from RxLight Database such as Merchants, Car Brands, Car Models and etc;
 * 2. These business data can be used in other APIs to unlock high-level features;
 * 3. Client can use these data on their App. For example, client can use CarBrandTreeList to help customers to choose their car model when they are ordering
 */
interface SystemDataManagementInterface
{

    /**
     * @param int $type 系统数据类型
     * 1: 车辆品牌列表
     * 2: 车型列表
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function GetSysData(int $type, int $page = 1, int $page_size = 10): array;
}
