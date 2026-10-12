<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class KeepConstructorsCodeFreeAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /**
     * @param array<string> $excludedPaths
     */
    public function __construct(
        array $excludedPaths = [],
    ) {
        $this->excludedPaths = $excludedPaths;
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

        if ($node->name->toString() !== '__construct') {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassMethod) {
            return [];
        }

        if (null === $node->stmts) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return [];
        }

        $className = $classReflection->getName();
        $methodName = $node->name->toString();

        $errors = [];
        $hasNonAssignmentCode = false;

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Expression) {
                // Allow parent constructor calls
                if ($stmt->expr instanceof Node\Expr\StaticCall
                    && $stmt->expr->class instanceof Node\Name
                    && $stmt->expr->class->toString() === 'parent'
                    && $stmt->expr->name instanceof Node\Identifier
                    && $stmt->expr->name->toString() === '__construct'
                ) {
                    continue;
                }

                // Check property assignments
                if ($stmt->expr instanceof Assign
                    && $stmt->expr->var instanceof Node\Expr\PropertyFetch
                    && $stmt->expr->var->var instanceof Node\Expr\Variable
                    && $stmt->expr->var->var->name === 'this'
                    && !$this->containsLogic($stmt->expr->expr)
                ) {
                    continue;
                }
            }

            if ($this->isAssertion($stmt)) {
                continue;
            }

            if (!$hasNonAssignmentCode) {
                $hasNonAssignmentCode = true;
                $errors = RuleErrorFactory::createErrorWithTips(
                    message: 'Constructor %s::%s() contains code other than property assignments or assertions, which violates the "code-free constructor" principle (Elegant Object principle).',
                    identifier: 'elegantObject.constructor.codeInConstructor',
                    messageParameters: [$className, $methodName],
                    tips: TipFactory::keepConstructorsCodeFree()->tips(),
                )->errors();
            }
        }

        return $errors;
    }

    private function isAssertion(Node $node): bool
    {
        if ($node instanceof Expression) {
            $expr = $node->expr;

            if ($expr instanceof Node\Expr\FuncCall) {
                $name = $expr->name;
                if ($name instanceof Node\Name) {
                    $functionName = $name->toString();
                    if (in_array(strtolower($functionName), ['assert', '\\assert'], true)) {
                        return true;
                    }
                }
            }

            // Asserts like Assertion::notNull(), Assert::isTrue(), etc.
            if ($expr instanceof Node\Expr\StaticCall) {
                $class = $expr->class;
                if ($class instanceof Node\Name) {
                    $className = $class->toString();
                    if (str_contains($className, 'Assert')) {
                        return true;
                    }
                }
            }

            // Handle chained Assert::that()->method()->method() calls
            if ($expr instanceof Node\Expr\MethodCall) {
                $var = $expr->var;
                while ($var instanceof Node\Expr\MethodCall) {
                    $var = $var->var;
                }
                if ($var instanceof Node\Expr\StaticCall) {
                    $class = $var->class;
                    if ($class instanceof Node\Name && str_contains($class->toString(), 'Assert')) {
                        return true;
                    }
                }

                // Method calls like $this->assertNotNull()
                if ($expr->name instanceof Node\Identifier) {
                    $methodName = $expr->name->toString();
                    if (str_starts_with($methodName, 'assert')) {
                        if ($expr->var instanceof Node\Expr\Variable && $expr->var->name === 'this') {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    private function containsLogic(Node $expr): bool
    {
        return $expr instanceof Node\Expr\BinaryOp ||
            $expr instanceof Node\Expr\FuncCall ||
            $expr instanceof Node\Expr\MethodCall ||
            $expr instanceof Node\Expr\Ternary ||
            $expr instanceof Node\Expr\BooleanNot ||
            $expr instanceof Node\Expr\Cast;
    }
}
