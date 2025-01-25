<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct\DeviceManagement;

use Guikejia\HeiMaCharge\Attributes\Map;
use Guikejia\HeiMaCharge\DataStruct\DataModel;

class DeviceProtocol extends DataModel
{
    #[Map('version')]
    public string $version;

    #[Map('type')]
    public int $type;
}
