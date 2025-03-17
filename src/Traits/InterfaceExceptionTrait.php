<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

use PHPStan\Reflection\ClassReflection;

trait InterfaceExceptionTrait
{
    /** @var array<string> */
    protected array $allowedInterfaces = [];

    public function isAllowedInterface(string $interfaceName): bool
    {
        foreach ($this->allowedInterfaces as $allowed) {
            if ($interfaceName === $allowed) {
                return true;
            }

            if ($allowed === '*Interface' && str_ends_with($interfaceName, 'Interface')) {
                return true;
            }

            if (str_contains($allowed, '*')) {
                $pattern = preg_quote($allowed, '/');
                $pattern = str_replace('\*', '.*', $pattern);
                $pattern = '/^' . $pattern . '$/';

                if (preg_match($pattern, $interfaceName)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isMethodFromAllowedInterface(?ClassReflection $classReflection, string $methodName): bool
    {
        if ($classReflection === null) {
            return false;
        }

        foreach ($classReflection->getInterfaces() as $interface) {
            $interfaceName = $interface->getName();
            if ($this->isAllowedInterface($interfaceName)) {
                $hasMethod = $interface->hasMethod($methodName);
                if ($hasMethod) {
                    return true;
                }
            }
        }

        $parentClass = $classReflection->getParentClass();
        if ($parentClass !== null) {
            if ($parentClass->isAbstract() && $parentClass->hasMethod($methodName)) {
                return true;
            }

            foreach ($parentClass->getInterfaces() as $interface) {
                $interfaceName = $interface->getName();
                if ($this->isAllowedInterface($interfaceName)) {
                    $hasMethod = $interface->hasMethod($methodName);
                    if ($hasMethod) {
                        return true;
                    }
                }
            }

            return $this->isMethodFromAllowedInterface($parentClass, $methodName);
        }

        return false;
    }
}
