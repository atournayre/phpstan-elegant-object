<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AvoidTypeIntrospection;

final class PlainConfigurable
{
    public function allowedMethod(mixed $value): bool
    {
        return is_int($value);
    }

    public function reported(mixed $value): bool
    {
        return is_array($value);
    }
}
