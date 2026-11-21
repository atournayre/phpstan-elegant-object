<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate;

class PublicConstructor
{
    public function __construct()
    {
    }
}

class ImplicitPublicConstructor
{
    function __construct()
    {
    }
}

class ProtectedConstructor
{
    protected function __construct()
    {
    }
}

class PromotedPublicConstructor
{
    public function __construct(private string $value)
    {
    }
}

class PrivateConstructor
{
    private function __construct()
    {
    }
}

class WithoutConstructor
{
}
