<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\OrderManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class ChargeStation extends DataModel
{
    /**
     * OpenRxLight目前支持三种充电基准模式：
     * 1. 充满：默认给用户车辆充满停止；
     * 2. 基于soc：给用户车辆充到某个soc值停止；
     * 3. * 基于金额：充到某个金额停止；
     * @var int|null
     */
    #[Map('charge_basic_type')]
    public ?int $charge_basic_type;

    /**
     * 充电基准值对应的值
     * 1. 基于soc：1-100，SOC值
     * 2. 基于金额：目标金额值
     * @var int|null
     */
    #[Map('charge_basic_value')]
    public ?int $charge_basic_value;

    /**
     * 用户下单车辆的modelId;
     * 通过 GetSysData 接口（其中 type=2）可以获取到该值;
     * 如果用户下单车辆的modelId能够提供, OpenRxLight 的调度系统可以更加精准地匹配到合适的 MPS，这样能够有效提升场站的运营效率
     * @var int|null
     */
    #[Map('car_model_id')]
    public ?int $car_model_id;
}
