<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\ShouldNotHappenException;

/**
 * Tests use Fakes (in-memory implementations), not mocks.
 *
 * Only test classes are analyzed: classes extending PHPUnit\Framework\TestCase or whose short name ends in "Test".
 * Flagged: $this->createMock() and its PHPUnit siblings, $this->prophesize(), Mockery::mock() and Mockery::spy().
 *
 * Exemptions are chosen by the developer: excluded paths, allowed method names (e.g. createStub)
 * and allowed test classes (wildcards).
 */
final class NoMockInTestsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;

    private const TEST_CASE = 'PHPUnit\Framework\TestCase';

    private const INSTANCE_METHODS = [
        'createmock',
        'createstub',
        'createconfiguredmock',
        'createconfiguredstub',
        'createpartialmock',
        'createtestproxy',
        'getmockbuilder',
        'createmockforintersectionofinterfaces',
        'createstubforintersectionofinterfaces',
        'prophesize',
    ];

    private const MOCKERY_METHODS = ['mock', 'spy', 'namedmock'];

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedClasses = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedClasses = $allowedClasses;
    }

    public function getNodeType(): string
    {
        return CallLike::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof MethodCall && !$node instanceof StaticCall) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null || !$this->isTestClass($classReflection)) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile()) || $this->isAllowedClass($classReflection->getName())) {
            return true;
        }

        $method = $this->mockMethod($node);

        return $method === null || $this->isAllowedMethodName($method);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $method = $this->mockMethod($node);
        if ($method === null) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Test %s calls %s() to create a mock. Use a Fake (an in-memory implementation) instead.',
            'elegantObject.tests.mock',
            [$classReflection->getName(), $method],
        )->errors();
    }

    /**
     * Name of the mock-creating method called by the node, as written in the code, or null.
     */
    private function mockMethod(Node $node): ?string
    {
        if ($node instanceof MethodCall) {
            if (!$node->var instanceof Variable || $node->var->name !== 'this' || !$node->name instanceof Identifier) {
                return null;
            }

            return in_array($node->name->toLowerString(), self::INSTANCE_METHODS, true)
                ? $node->name->toString()
                : null;
        }

        if ($node instanceof StaticCall && $node->class instanceof Name && $node->name instanceof Identifier) {
            $isMockery = strtolower(ltrim($node->class->toString(), '\\')) === 'mockery';

            return $isMockery && in_array($node->name->toLowerString(), self::MOCKERY_METHODS, true)
                ? 'Mockery::' . $node->name->toString()
                : null;
        }

        return null;
    }

    private function isTestClass(ClassReflection $classReflection): bool
    {
        return $classReflection->isSubclassOf(self::TEST_CASE) || str_ends_with($classReflection->getName(), 'Test');
    }

    private function isAllowedClass(string $className): bool
    {
        foreach ($this->allowedClasses as $allowed) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($allowed, '/')) . '$/';
            if (preg_match($pattern, $className) === 1) {
                return true;
            }
        }

        return false;
    }
}
