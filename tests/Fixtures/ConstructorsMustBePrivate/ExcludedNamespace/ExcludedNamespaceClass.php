<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate\ExcludedNamespace;

class ExcludedNamespaceClass
{
    public function __construct()
    {
    }
}
