<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

use PHPStan\Analyser\Scope;

trait SecondaryConstructorTrait
{
    /** @var array<string> */
    private array $secondaryConstructorPrefixes = ['new', 'from', 'create', 'of', 'with', 'static'];

    private function isConstructor(string $methodName): bool
    {
        return $methodName === '__construct';
    }

    private function isSecondaryConstructor(string $methodName, Scope $scope): bool
    {
        if (empty($methodName)) {
            return false;
        }

        foreach ($this->secondaryConstructorPrefixes as $prefix) {
            if (!is_string($prefix)) {
                continue;
            }
            if (str_starts_with($methodName, $prefix)) {
                return true;
            }
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        $methodReflection = $classReflection->hasMethod($methodName)
            ? $classReflection->getMethod($methodName, $scope)
            : null;

        if ($methodReflection === null || !$methodReflection->isStatic()) {
            return false;
        }

        return false;
    }
}
