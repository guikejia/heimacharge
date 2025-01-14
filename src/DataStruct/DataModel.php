<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge\DataStruct;

use Guikejia\HeiMaCharge\Attributes\Map;

class DataModel
{
    public function __construct(array $data)
    {
        foreach ((new \ReflectionClass($this))->getProperties() as $property) {
            $attributes = $property->getAttributes(Map::class);
            if (!empty($attributes)) {
                $mapName = $attributes[0]->newInstance()->field;
                $type = $property->getType();
                if ($type && !$type->isBuiltin()) {
                    // 如果字段是类，递归实例化
                    $nestedClass = $type->getName();
                    $property->setValue($this, new $nestedClass($data[$mapName]));
                } else {
                    // 普通字段直接赋值
                    $property->setValue($this, $data[$mapName] ?? null);
                }
            }
        }
    }
}
