<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\ClassExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * Throw only specific domain exceptions, never generic built-in ones.
 *
 * Opt-in project convention, NOT an Elegant Objects rule: it deviates from EO 4.2.5 ("Just one exception type
 * is enough"). It forbids `throw new <generic built-in exception>` and, optionally, requires every thrown
 * class to implement a given interface. Rethrowing a caught variable (`throw $e`) is never flagged.
 */
final class NeverThrowGenericExceptionsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use ClassExceptionTrait;
    use TestClassTrait;

    private const GENERIC_EXCEPTIONS = [
        'exception',
        'error',
        'runtimeexception',
        'logicexception',
        'invalidargumentexception',
        'domainexception',
        'lengthexception',
        'outofrangeexception',
        'outofboundsexception',
        'rangeexception',
        'overflowexception',
        'underflowexception',
        'unexpectedvalueexception',
        'badfunctioncallexception',
        'badmethodcallexception',
    ];

    /** @var array<string> */
    private array $allowedExceptions;

    private ?string $requiredInterface;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedExceptions Thrown class names (wildcards) that are tolerated
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedClasses Classes (exact names) in which throwing is not checked
     * @param string|null $requiredInterface When set, every thrown class must implement it
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedExceptions = [],
        array $allowedMethodNames = [],
        array $allowedClasses = [],
        ?string $requiredInterface = null,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedExceptions = $allowedExceptions;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedClasses = $allowedClasses;
        $this->requiredInterface = $requiredInterface !== null ? ltrim($requiredInterface, '\\') : null;
    }

    public function getNodeType(): string
    {
        return Throw_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Throw_ || !$node->expr instanceof New_ || !$node->expr->class instanceof Name) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $functionName = $scope->getFunctionName();
        if ($functionName !== null && $this->isAllowedMethodName($functionName)) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        return $this->isTestClass($classReflection) || $this->isAllowedClassName($classReflection->getName());
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Throw_ || !$node->expr instanceof New_ || !$node->expr->class instanceof Name) {
            return [];
        }

        $thrown = ltrim($node->expr->class->toString(), '\\');
        if ($this->isAllowedException($thrown)) {
            return [];
        }

        if (in_array(strtolower($thrown), self::GENERIC_EXCEPTIONS, true)) {
            return RuleErrorFactory::createErrorWithTips(
                'Generic exception %s must not be thrown. Throw a specific domain exception instead.',
                'elegantObject.exceptions.generic',
                [$thrown],
            )->errors();
        }

        if ($this->requiredInterface === null || $this->implementsRequiredInterface($node->expr, $scope)) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Exception %s must implement %s.',
            'elegantObject.exceptions.interface',
            [$thrown, $this->requiredInterface],
        )->errors();
    }

    private function implementsRequiredInterface(New_ $new, Scope $scope): bool
    {
        $reflections = $scope->getType($new)->getObjectClassReflections();
        if ($reflections === []) {
            return true;
        }

        foreach ($reflections as $reflection) {
            if (!$reflection->is((string) $this->requiredInterface)) {
                return false;
            }
        }

        return true;
    }

    private function isAllowedException(string $className): bool
    {
        foreach ($this->allowedExceptions as $allowed) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote(ltrim($allowed, '\\'), '/')) . '$/i';
            if (preg_match($pattern, $className) === 1) {
                return true;
            }
        }

        return false;
    }
}
