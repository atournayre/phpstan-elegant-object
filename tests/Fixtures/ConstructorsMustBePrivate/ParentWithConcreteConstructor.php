<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate;

abstract class ParentWithConcreteConstructor
{
    private function __construct()
    {
    }
}
