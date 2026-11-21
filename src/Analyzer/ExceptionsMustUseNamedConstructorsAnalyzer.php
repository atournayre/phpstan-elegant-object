<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\ShouldNotHappenException;

/**
 * Exceptions are built through named constructors, e.g. `UserInvalid::becauseEmailIsMissing()`.
 * Project convention, opt-in, not from the Elegant Objects book (it sits apart from EO 4.2.5).
 *
 * Flags:
 * - `new X(...)` where X is a user-defined Throwable, unless written inside class X itself (its named constructors);
 * - a user-defined Throwable declaring a non-private constructor.
 * Built-in and vendor exceptions are never flagged (see NeverThrowGenericExceptionsRule for generic ones).
 *
 * Overlap: the second check is also reported by ConstructorsMustBePrivateRule (for every class, not only exceptions),
 * under a different identifier. Disable one of them if the duplicate message is unwanted.
 */
final class ExceptionsMustUseNamedConstructorsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    /** @var array<string> */
    private array $excludedNamespaces;

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedNamespaces
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedNamespaces = [],
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->excludedNamespaces = array_map(
            static fn (string $namespace): string => trim($namespace, '\\'),
            $excludedNamespaces,
        );
        $this->allowedClasses = $allowedClasses;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$this->isNewExpression($node) && !$this->isNonPrivateConstructor($node)) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $current = $scope->getClassReflection();

        return $current !== null && $this->isTestClass($current);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if ($node instanceof New_) {
            return $this->analyzeNew($node, $scope);
        }

        if ($node instanceof ClassMethod) {
            return $this->analyzeConstructor($scope);
        }

        return [];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    private function analyzeNew(New_ $node, Scope $scope): array
    {
        $current = $scope->getClassReflection();

        foreach ($scope->getType($node)->getObjectClassReflections() as $thrown) {
            if (!$thrown->isSubclassOf(\Throwable::class) && $thrown->getName() !== \Throwable::class) {
                continue;
            }

            if ($this->isVendorOrInternal($thrown)) {
                continue;
            }

            if ($current !== null && $current->getName() === $thrown->getName()) {
                continue;
            }

            if ($this->isTolerated($thrown)) {
                continue;
            }

            return RuleErrorFactory::createErrorWithTips(
                'Exception %s is built with the new operator. Use a named constructor instead (e.g. %s::because...()).',
                'elegantObject.exception.newOperatorUsed',
                [$thrown->getName(), $thrown->getName()],
            )->errors();
        }

        return [];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    private function analyzeConstructor(Scope $scope): array
    {
        $class = $scope->getClassReflection();
        if ($class === null || $class->isInterface() || $class->isEnum() || $class->isAnonymous()) {
            return [];
        }

        if (!$class->isSubclassOf(\Throwable::class) || $this->isVendorOrInternal($class) || $this->isTolerated($class)) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Exception %s must not expose a constructor. Make it private and expose named constructors.',
            'elegantObject.exception.publicConstructor',
            [$class->getName()],
        )->errors();
    }

    private function isNewExpression(Node $node): bool
    {
        return $node instanceof New_ && !$node->class instanceof Node\Stmt\Class_;
    }

    private function isNonPrivateConstructor(Node $node): bool
    {
        return $node instanceof ClassMethod
            && $node->name->toLowerString() === '__construct'
            && !$node->isPrivate()
            && !$node->isAbstract();
    }

    private function isTolerated(ClassReflection $class): bool
    {
        return $this->isAllowedClass($class->getName())
            || $this->isInExcludedNamespace($class->getName())
            || $this->hasAllowedInterface($class);
    }

    private function isVendorOrInternal(ClassReflection $class): bool
    {
        $file = $class->getFileName();

        return $class->isInternal() || $file === null || str_contains($file, '/vendor/');
    }

    private function isAllowedClass(string $className): bool
    {
        foreach ($this->allowedClasses as $allowed) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote(ltrim($allowed, '\\'), '/')) . '$/';
            if (preg_match($pattern, $className) === 1) {
                return true;
            }
        }

        return false;
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
}
