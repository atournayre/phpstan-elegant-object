<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\Catch_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * Catch specific exceptions only (error handling, chapter number unverified).
 *
 * Flags every catch type (each member of a multi-catch) that is \Throwable, \Exception, \Error,
 * \RuntimeException or \LogicException. Rethrow-only handlers are flagged as well.
 *
 * Exemptions are chosen by the developer: excluded paths, allowed exceptions (wildcards),
 * method names, classes (wildcards) and interfaces (wildcards). Test classes are skipped.
 */
final class NoGenericCatchAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    private const GENERIC = ['throwable', 'exception', 'error', 'runtimeexception', 'logicexception'];

    /** @var array<string> */
    private array $allowedExceptions;

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedExceptions
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedExceptions = [],
        array $allowedMethodNames = [],
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedExceptions = $allowedExceptions;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedClasses = $allowedClasses;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return Catch_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Catch_ || $this->isExcludedPath($scope->getFile())) {
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

        return $this->isTestClass($classReflection)
            || $this->matchesAny($classReflection->getName(), $this->allowedClasses)
            || $this->hasAllowedInterface($classReflection);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Catch_) {
            return [];
        }

        $errors = [];
        foreach ($node->types as $type) {
            $name = ltrim($type->toString(), '\\');
            if (!in_array(strtolower($name), self::GENERIC, true) || $this->matchesAny($name, $this->allowedExceptions)) {
                continue;
            }

            $errors[] = RuleErrorFactory::createErrorWithTips(
                'Catching generic %s is forbidden. Catch a specific exception instead.',
                'elegantObject.errorHandling.genericCatch',
                [$name],
            )->errors();
        }

        return array_merge(...$errors);
    }

    /**
     * @param array<string> $patterns
     */
    private function matchesAny(string $subject, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $regex = '/^' . str_replace('\*', '.*', preg_quote(ltrim($pattern, '\\'), '/')) . '$/';
            if (preg_match($regex, $subject) === 1) {
                return true;
            }
        }

        return false;
    }
}
