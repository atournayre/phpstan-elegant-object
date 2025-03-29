<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExposeFewPublicMethods;

/**
 * This class violates the rule: it has 7 public methods
 */
class ClassWithTooManyPublicMethods
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

    public function publicMethod6(): void
    {
    }

    public function publicMethod7(): void
    {
    }

    public static function newOne(): self
    {
        return new self();
    }

    public static function new2(): self
    {
        return new self();
    }

    protected function protectedMethod(): void
    {
    }

    private function privateMethod(): void
    {
    }
}
