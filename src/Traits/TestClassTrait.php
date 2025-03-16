<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

use PHPStan\Reflection\ClassReflection;

trait TestClassTrait
{
    public function isTestClass(ClassReflection $classReflection): bool
    {
        $shortClassName = substr($classReflection->getName(), strrpos($classReflection->getName(), '\\') + 1);
        return str_contains($shortClassName, 'Test') || str_contains($shortClassName, 'InMemory');
    }
}
