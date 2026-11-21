<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate;

class ExtendsClassImplementingAllowedInterface extends ImplementsAllowedInterface
{
    public function __construct()
    {
    }
}
