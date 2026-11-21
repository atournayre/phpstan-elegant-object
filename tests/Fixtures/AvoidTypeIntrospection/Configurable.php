<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AvoidTypeIntrospection;

final class Configurable implements TolerantInterface
{
    public function handle(mixed $value): bool
    {
        return is_int($value);
    }
}
