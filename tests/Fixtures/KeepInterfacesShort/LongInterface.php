<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepInterfacesShort;

/**
 * This interface violates the rule: it has 4 methods
 */
interface LongInterface
{
    public function method1(): void;
    public function method2(): void;
    public function method3(): void;
    public function method4(): void;
}
