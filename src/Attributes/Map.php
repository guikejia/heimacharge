<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\Attributes;

use Attribute;

#[Attribute]
class Map {
    public function __construct(public readonly string $field) {}
}
