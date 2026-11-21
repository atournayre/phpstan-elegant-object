<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AvoidTypeIntrospection;

final class Example
{
    public function check(object $value): bool
    {
        return $value instanceof \Countable;
    }

    public function functions(mixed $value): string
    {
        if (is_string($value)) {
            return get_class($value);
        }

        return gettype($value);
    }

    public function casts(mixed $value): int
    {
        return (int) $value;
    }

    public function classComparison(object $left, object $right): bool
    {
        return $left::class === $right::class;
    }

    public function staticClass(object $other): bool
    {
        return static::class !== $other::class;
    }

    public function clean(string $value): string
    {
        return strtoupper($value);
    }

    public function plainClassName(): string
    {
        return self::class;
    }
}
