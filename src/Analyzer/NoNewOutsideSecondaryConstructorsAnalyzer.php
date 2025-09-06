<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\SecondaryConstructorTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class NoNewOutsideSecondaryConstructorsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use InterfaceExceptionTrait;
    use SecondaryConstructorTrait;

    /** @var array<string> */
    private array $excludedClasses;

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
        $classReflection = $scope->getClassReflection();
        $methodName = $scope->getFunction()?->getName() ?? '';

        return !$node instanceof New_
            || $this->isExcludedPath($scope->getFile())
            || $classReflection === null
            || $this->hasAllowedInterface($classReflection)
            || $this->isExcludedClass($classReflection->getName())
            || $methodName === '__construct'
            || $this->isSecondaryConstructor($methodName, $scope);
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
        $methodName = $scope->getFunction()?->getName() ?? '';

        $instantiatedClass = '';
        if ($node->class instanceof Node\Name) {
            $instantiatedClass = $node->class->toString();
        }

        return RuleErrorFactory::createErrorWithTips(
            'The operator new is used in %s::%s() for class %s, which violates object encapsulation. Use secondary constructors (static factory methods) instead.',
            'elegantObject.constructor.newOperatorUsed',
            [$className, $methodName, $instantiatedClass],
            TipFactory::noNewOutsideSecondaryConstructors()->tips(),
        )->errors();
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
