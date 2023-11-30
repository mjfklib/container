<?php

declare(strict_types=1);

namespace mjfklib\Container;

final class ObjectFactory
{
    /**
     * @template T
     * @param mixed $values
     * @param class-string<T> $className
     * @param (callable(mixed[] $values): T) $construct
     * @return T
     */
    public static function createObject(
        mixed $values,
        string $className,
        callable $construct
    ): mixed {
        try {
            if (is_object($values)) {
                if (is_a($values, $className, false)) {
                    return $values;
                }
                $values = get_object_vars($values);
            } elseif (!is_array($values)) {
                $values = ['values' => $values];
            }
            return $construct($values);
        } catch (\Throwable $t) {
            throw new \RuntimeException("Error creating instance: {$className}}", 0, $t);
        }
    }
}
