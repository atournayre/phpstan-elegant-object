<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate;

final class ExtendsConcreteConstructor extends ParentWithConcreteConstructor
{
    public function __construct()
    {
    }
}
