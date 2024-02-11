<?php

namespace veejay\api\component;

use ReflectionException;
use ReflectionProperty;

abstract class Component
{
    /**
     * @param array $properties
     * @throws ReflectionException
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * Установить публичные свойства объекта.
     * @param array $properties
     * @return void
     * @throws ReflectionException
     */
    public function setProperties(array $properties): void
    {
        foreach ($properties as $name => $value) {
            if (!property_exists($this, $name)) {
                continue;
            }

            $reflectionMethod = new ReflectionProperty($this, $name);

            if ($reflectionMethod->isPublic() && !$reflectionMethod->isStatic()) {
                $this->$name = $value;
            }
        }
    }
}
