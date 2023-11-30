<?php

declare(strict_types=1);

namespace mjfklib\Container;

final class ArrayValue
{
    /** @var array<int,string> */
    private const TRUE_VALUES = [
        '1',
        'ON',
        'T',
        'TRUE',
        'X',
        'Y',
        'YES'
    ];


    /**
     * @param string $name
     * @return \ValueError
     */
    private static function err(string $name): \ValueError
    {
        return new \ValueError("Unable to get value: {$name}");
    }


    /**
     * @template T
     * @param mixed[] $values
     * @param string $name
     * @param (callable(mixed $v): bool) $isValue
     * @param (callable(mixed $v): T) $castValue
     * @return T
     */
    private static function getValue(
        array $values,
        string $name,
        callable $isValue,
        callable $castValue
    ): mixed {
        $value = $values[$name] ?? null;
        if (!$isValue($value)) {
            $value = $castValue($value);
        }
        /** @var T $value */
        return $value;
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return bool
     */
    public static function getBool(
        array $values,
        string $name
    ): bool {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_bool($v),
            fn ($v) => self::getBoolValue($v) ?? throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return bool|null
     */
    public static function getBoolNull(
        array $values,
        string $name
    ): bool|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_bool($v) || is_null($v),
            fn ($v) => self::getBoolValue($v) ?? self::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return float
     */
    public static function getFloat(
        array $values,
        string $name
    ): float {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_float($v),
            fn ($v) => self::getFloatValue($v) ?? throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return float|null
     */
    public static function getFloatNull(
        array $values,
        string $name
    ): float|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_float($v) || is_null($v),
            fn ($v) => self::getFloatValue($v) ?? self::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return int
     */
    public static function getInt(
        array $values,
        string $name
    ): int {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_int($v),
            fn ($v) => self::getIntValue($v) ?? throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return int
     */
    public static function getIntNull(
        array $values,
        string $name
    ): int|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_int($v) || is_null($v),
            fn ($v) => self::getIntValue($v) ?? self::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return string
     */
    public static function getString(
        array $values,
        string $name
    ): string {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_string($v),
            fn ($v) => self::getStringValue($v) ?? throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return string|null
     */
    public static function getStringNull(
        array $values,
        string $name
    ): string|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_string($v) || is_null($v),
            fn ($v) => self::getStringValue($v) ?? self::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return mixed[]
     */
    public static function getArray(
        array $values,
        string $name
    ): array {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_array($v),
            fn ($v) => static::getArrayValue($v) ?? throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return mixed[]|null
     */
    public static function getArrayNull(
        array $values,
        string $name
    ): array|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_array($v) || is_null($v),
            fn ($v) => static::getArrayValue($v) ?? static::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return object
     */
    public static function getObject(
        array $values,
        string $name
    ): object {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_object($v),
            fn ($v) => self::getObjectValue($v) ?? throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return object|null
     */
    public static function getObjectNull(
        array $values,
        string $name
    ): object|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_object($v),
            fn ($v) => self::getObjectValue($v) ?? self::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return \DateTimeInterface
     */
    public static function getDateTime(
        array $values,
        string $name
    ): \DateTimeInterface {
        return self::getValue(
            $values,
            $name,
            fn ($v) => $v instanceof \DateTimeInterface,
            fn ($v) => self::getDateTimeValue($v) ??  throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return \DateTimeInterface
     */
    public static function getDateTimeNull(
        array $values,
        string $name
    ): \DateTimeInterface|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => $v instanceof \DateTimeInterface || is_null($v),
            fn ($v) => self::getDateTimeValue($v) ?? self::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return resource
     */
    public static function getResource(
        array $values,
        string $name
    ): mixed {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_resource($v),
            fn ($v) => is_resource($v) ? $v : throw self::err($name)
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return resource|null
     */
    public static function getResourceNull(
        array $values,
        string $name
    ): mixed {
        return self::getValue(
            $values,
            $name,
            fn ($v) => is_resource($v) || is_null($v),
            fn ($v) => is_resource($v) ? $v : self::getNullValue($name, $v)
        );
    }


    /**
     * @param mixed[]|string|object|null $values
     * @param mixed[]|string|object|null $name
     * @return array<string,string>
     */
    public static function getStringArray(
        array|string|object|null $values,
        array|string|object|null $name = null
    ): array {
        if (is_array($values) && is_string($name)) {
            return self::getValue(
                $values,
                $name,
                fn () => false,
                fn ($v) => self::getStringArrayValue($v) ?? throw self::err($name)
            );
        }

        return array_merge(
            self::getStringArrayValue($name) ?? [],
            self::getStringArrayValue($values) ?? []
        );
    }


    /**
     * @param mixed[] $values
     * @param string $name
     * @return array<string,string>|null
     */
    public static function getStringArrayNull(
        array $values,
        string $name
    ): array|null {
        return self::getValue(
            $values,
            $name,
            fn ($v) => false,
            fn ($v) => self::getStringArrayValue($v) ?? self::getNullValue($name, $v)
        );
    }


/**********************************************************************************************************************/


    /**
     * @param string $name
     * @param mixed $value
     * @return null
     */
    private static function getNullValue(
        string $name,
        mixed $value
    ): null {
        return is_null($value) ? null : throw self::err($name);
    }


    /**
     * @param mixed $value
     * @return bool|null
     */
    private static function getBoolValue(mixed $value): bool|null
    {
        return is_scalar($value) ? in_array(strtoupper(strval($value)), self::TRUE_VALUES, true) : null;
    }


    /**
     * @param mixed $value
     * @return float|null
     */
    private static function getFloatValue(mixed $value): float|null
    {
        return is_scalar($value) ? floatval($value) : null;
    }


    /**
     * @param mixed $value
     * @return int|null
     */
    private static function getIntValue(mixed $value): int|null
    {
        return is_scalar($value) ? intval($value) : null;
    }


    /**
     * @param mixed $value
     * @return string|null
     */
    private static function getStringValue(mixed $value): string|null
    {
        return is_scalar($value) ? strval($value) : null;
    }


    /**
     * @param mixed $value
     * @return mixed[]|null
     */
    private static function getArrayValue(mixed $value): array|null
    {
        if (is_object($value)) {
            $value = get_object_vars($value);
        } elseif (is_string($value)) {
            if (is_file($value)) {
                $fileContents = file_get_contents($value);
                if (!is_string($fileContents)) {
                    throw new \RuntimeException("Error reading file: {$value}");
                }
                $value = $fileContents;
            }

            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($value)) {
                throw new \RuntimeException("Parsed value is not an array");
            }
        }

        return is_array($value) ? $value : null;
    }


    /**
     * @param mixed $value
     * @return array<string,string>|null
     */
    private static function getStringArrayValue(mixed $value): array|null
    {
        $value = self::getArrayValue($value);
        if (!is_array($value)) {
            return null;
        }

        $value = array_filter(
            $value,
            fn ($v) => is_scalar($v) || $v instanceof \Stringable
        );

        return array_column(
            array_map(
                fn ($v, $k) => [strval($v), strval($k)],
                array_values($value),
                array_keys($value)
            ),
            0,
            1
        );
    }


    /**
     * @param mixed $value
     * @return object|null
     */
    private static function getObjectValue(mixed $value): object|null
    {
        $value = match (true) {
            is_object($value) => $value,
            is_array($value) => (object)$value,
            is_string($value) => @json_decode($value, null, 512),
            default => null
        };

        return is_object($value) ? $value : null;
    }


    /**
     * @param mixed $value
     * @return \DateTimeInterface
     */
    private static function getDateTimeValue(mixed $value): \DateTimeInterface|null
    {
        return match (true) {
            is_object($value) && $value instanceof \DateTimeInterface => $value,
            is_int($value) => new \DateTimeImmutable(date('c', $value)),
            is_string($value) => new \DateTimeImmutable($value),
            default => null
        };
    }
}
