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
        if (in_array($interfaceName, $this->allowedInterfaces, true)) {
            return true;
        }

        foreach ($this->allowedInterfaces as $pattern) {
            if (str_contains($pattern, '*')) {
                if ($pattern === '*Interface') {
                    if (str_ends_with($interfaceName, 'Interface')) {
                        return true;
                    }
                    continue;
                }

                $pattern = str_replace('\\', '\\\\', $pattern);
                $pattern = str_replace('**', '.*', $pattern);
                $pattern = str_replace('*', '[^\\\\]+', $pattern);
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

        if ($this->isAllowedInterface($classReflection->getName())) {
            return true;
        }

        $parentClass = $classReflection->getParentClass();
        if ($parentClass !== null && $parentClass->isAbstract() && $parentClass->hasMethod($methodName)) {
            return true;
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

        if ($parentClass !== null) {
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
