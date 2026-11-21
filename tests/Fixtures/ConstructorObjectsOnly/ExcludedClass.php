<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorObjectsOnly;

final class ExcludedClass
{
    public function __construct(string $value)
    {
    }
}
