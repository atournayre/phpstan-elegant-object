<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExposeFewPublicMethods;

/**
 * This class respects the rule: it has exactly 5 public methods
 */
class ClassWithFewPublicMethods
{
    public function publicMethod1(): void
    {
    }

    public function publicMethod2(): void
    {
    }

    public function publicMethod3(): void
    {
    }

    public function publicMethod4(): void
    {
    }

    public function publicMethod5(): void
    {
    }

    protected function protectedMethod(): void
    {
    }

    private function privateMethod(): void
    {
    }
}
