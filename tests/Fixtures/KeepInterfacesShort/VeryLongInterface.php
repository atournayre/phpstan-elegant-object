<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepInterfacesShort;
/**
 * This interface violates the rule severely: it has 8 methods
 */
interface VeryLongInterface
{
    public function method1(): void;
    public function method2(): void;
    public function method3(): void;
    public function method4(): void;
    public function method5(): void;
    public function method6(): void;
    public function method7(): void;
    public function method8(): void;
}
