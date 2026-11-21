<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate;

class ImplementsAllowedInterface implements FrameworkInterface
{
    public function __construct()
    {
    }
}
