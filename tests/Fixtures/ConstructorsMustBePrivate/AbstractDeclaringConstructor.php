<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate;

abstract class AbstractDeclaringConstructor
{
    abstract public function __construct(string $value);
}
