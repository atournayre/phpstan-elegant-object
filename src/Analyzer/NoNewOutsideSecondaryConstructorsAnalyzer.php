<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class NoNewOutsideSecondaryConstructorsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use InterfaceExceptionTrait;

    /** @var array<string> */
    private array $excludedClasses;

    /** @var array<string> */
    private array $secondaryConstructorPrefixes;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedInterfaces
     * @param array<string> $excludedClasses
     * @param array<string> $secondaryConstructorPrefixes
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedInterfaces = [],
        array $excludedClasses = [],
        array $secondaryConstructorPrefixes = ['new', 'from', 'create', 'of', 'with'],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedInterfaces = $allowedInterfaces;
        $this->excludedClasses = $excludedClasses;
        $this->secondaryConstructorPrefixes = $secondaryConstructorPrefixes;
    }

    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof New_) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return true;
        }

        if ($this->hasAllowedInterface($classReflection)) {
            return true;
        }

        if ($this->isExcludedClass($classReflection->getName())) {
            return true;
        }

        // Get current method name
        $methodName = '';
        $function = $scope->getFunction();
        if ($function !== null) {
            $methodName = $function->getName();
        }

        // Allow new in __construct
        if ($methodName === '__construct') {
            return true;
        }

        // Allow new in static methods that match secondary constructor patterns
        if ($this->isSecondaryConstructor($methodName, $scope)) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof New_) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return [];
        }

        $className = $classReflection->getName();

        // Get method name safely
        $methodName = '';
        $function = $scope->getFunction();
        if ($function !== null) {
            $methodName = $function->getName();
        }

        $instantiatedClass = '';
        if ($node->class instanceof Node\Name) {
            $instantiatedClass = $node->class->toString();
        }

        return RuleErrorFactory::createErrorWithTips(
            'The operator new is used in %s::%s() for class %s, which violates object encapsulation. Use secondary constructors (static factory methods) instead.',
            [$className, $methodName, $instantiatedClass],
            TipFactory::noNewOutsideSecondaryConstructors()->tips(),
        )->errors();
    }

    private function isSecondaryConstructor(string $methodName, Scope $scope): bool
    {
        if (empty($methodName)) {
            return false;
        }

        // Check if the method is static
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

        // Check if method name starts with any of the configured prefixes
        foreach ($this->secondaryConstructorPrefixes as $prefix) {
            if (str_starts_with($methodName, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function isExcludedClass(string $className): bool
    {
        foreach ($this->excludedClasses as $excludedClass) {
            if ($className === $excludedClass || is_subclass_of($className, $excludedClass)) {
                return true;
            }
        }

        return false;
    }
}
