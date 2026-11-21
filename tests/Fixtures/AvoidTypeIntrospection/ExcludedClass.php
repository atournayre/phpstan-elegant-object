<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AvoidTypeIntrospection;

final class ExcludedClass
{
    public function handle(mixed $value): bool
    {
        return $value instanceof \Countable;
    }
}
