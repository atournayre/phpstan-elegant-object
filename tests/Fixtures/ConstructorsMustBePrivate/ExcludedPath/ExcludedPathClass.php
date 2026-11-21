<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate\ExcludedPath;

class ExcludedPathClass
{
    public function __construct()
    {
    }
}
