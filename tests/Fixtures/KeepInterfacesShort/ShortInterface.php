<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepInterfacesShort;

/**
 * This interface respects the rule: it has 3 methods
 */
interface ShortInterface
{
    public function method1(): void;
    public function method2(): void;
    public function method3(): void;
}
