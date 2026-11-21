<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\TrinaryLogic;

final class ConstructorsMustBePrivateAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use InterfaceExceptionTrait;

    /** @var array<string> */
    private array $excludedNamespaces;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedNamespaces
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedNamespaces = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->excludedNamespaces = array_map(
            static fn (string $namespace): string => trim($namespace, '\\'),
            $excludedNamespaces,
        );
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof ClassMethod) {
            return true;
        }

        if ($node->name->toLowerString() !== '__construct') {
            return true;
        }

        if ($node->isPrivate() || $node->isAbstract()) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null || $classReflection->isInterface()) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        if ($this->isInExcludedNamespace($classReflection->getName())) {
            return true;
        }

        if ($this->hasAllowedInterface($classReflection)) {
            return true;
        }

        return $this->isRequiredByContract($classReflection);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassMethod) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            message: 'Constructor %s::__construct() must be private (found %s). Use a named constructor (static factory) instead.',
            identifier: 'elegantObject.constructor.nonPrivateConstructor',
            messageParameters: [
                $classReflection->getName(),
                $node->isProtected() ? 'protected' : 'public',
            ],
        )->errors();
    }

    private function isInExcludedNamespace(string $className): bool
    {
        foreach ($this->excludedNamespaces as $namespace) {
            if ($namespace !== '' && str_starts_with($className, $namespace . '\\')) {
                return true;
            }
        }

        return false;
    }

    /**
     * A constructor declared by an interface or an abstract parent cannot be made private.
     */
    private function isRequiredByContract(ClassReflection $classReflection): bool
    {
        foreach ($classReflection->getInterfaces() as $interface) {
            if ($interface->hasNativeMethod('__construct')) {
                return true;
            }
        }

        for ($parent = $classReflection->getParentClass(); $parent !== null; $parent = $parent->getParentClass()) {
            if (!$parent->hasNativeMethod('__construct')) {
                continue;
            }

            // ExtendedMethodReflection::isAbstract() is documented as TrinaryLogic|bool
            $isAbstract = $parent->getNativeMethod('__construct')->isAbstract();
            if ($isAbstract instanceof TrinaryLogic ? $isAbstract->yes() : $isAbstract) {
                return true;
            }
        }

        return false;
    }
}
