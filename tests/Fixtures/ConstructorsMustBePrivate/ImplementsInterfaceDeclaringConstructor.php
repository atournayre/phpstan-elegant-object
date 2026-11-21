<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate;

final class ImplementsInterfaceDeclaringConstructor implements DeclaresConstructorInterface
{
    public function __construct(private string $value)
    {
    }
}
