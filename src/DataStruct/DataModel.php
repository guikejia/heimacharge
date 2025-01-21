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
            if (empty($attributes)) {
                continue;
            }

            $mapName = $attributes[0]->newInstance()->field;
            $mapType = $attributes[0]->newInstance()->type;
            $type = $property->getType();
            $params = $data[$mapName] ?? null;

            // 内置类型
            if ($type && ! $type->isBuiltin()) {
                $nestedClass = $type->getName();
                if ($params) {
                    $property->setValue($this, new $nestedClass($params));
                    continue;
                }
                $property->setValue($this, null);
                continue;
            }

            // 数组类型
            if ($mapType && $type->getName() === 'array') {
                if ($params && is_array($params)) {
                    $property->setValue($this, array_map(fn ($item) => new $mapType($item), $params));
                    continue;
                }
            }

            // 普通类型及其他情况
            $property->setValue($this, $data[$mapName] ?? null);
        }
    }
}
