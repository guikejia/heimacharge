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
            if (! empty($attributes)) {
                $mapName = $attributes[0]->newInstance()->field;
                $mapType = $attributes[0]->newInstance()->type;
                $type = $property->getType();
                if ($type && ! $type->isBuiltin()) {
                    // 如果字段是类，递归实例化
                    $nestedClass = $type->getName();
                    $property->setValue($this, new $nestedClass($data[$mapName] ?? []));
                } elseif ($mapType && $type->getName() === 'array') {
                    if (is_array($data[$mapName])) {
                        $property->setValue($this, array_map(fn ($item) => new $mapType($item), $data[$mapName]));
                    }
                } else {
                    // 普通字段直接赋值
                    $property->setValue($this, $data[$mapName] ?? null);
                }
            }
        }
    }
}
