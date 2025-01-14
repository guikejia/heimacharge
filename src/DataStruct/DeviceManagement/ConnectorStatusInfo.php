<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\DeviceManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class ConnectorStatusInfo extends DataModel
{
    /**
     * 实时A相电流；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('a_phase_current')]
    public ?float $a_phase_current;

    /**
     * 实时A相功率；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('a_phase_power')]
    public ?float $a_phase_power;

    /**
     * 实时A相电压；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('a_phase_voltage')]
    public ?float $a_phase_voltage;

    /**
     * 实时B相电流；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('b_phase_current')]
    public ?float $b_phase_current;

    /**
     * 实时B相功率；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('b_phase_power')]
    public ?float $b_phase_power;

    /**
     * 实时B相电压；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('b_phase_voltage')]
    public ?float $b_phase_voltage;

    /**
     * 实时C相电流；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('c_phase_current')]
    public ?float $c_phase_current;

    /**
     * 实时C相功率；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('c_phase_power')]
    public ?float $c_phase_power;

    /**
     * 实时C相电压；如果接口是DC接口，这个值为空
     * @var float|null
     */
    #[Map('c_phase_voltage')]
    public ?float $c_phase_voltage;

    /**
     * 接口id
     * @var string
     */
    #[Map('connector_id')]
    public string $connector_id;

    /**
     * 接口实时电流
     * @var float|null
     */
    #[Map('current')]
    public ?float $current;

    /**
     * 接口实时功率
     * @var float|null
     */
    #[Map('power')]
    public ?float $power;

    /**
     * 接口状态
     * @var int|null
     */
    #[Map('status')]
    public ?int $status;

    /**
     * 接口类型
     * 1 AC充电口
     * 2 DC充电口
     * @var int
     */
    #[Map('type')]
    public int $type;

    /**
     * 接口实时电压
     * @var float|null
     */
    #[Map('voltage')]
    public ?float $voltage;
}
