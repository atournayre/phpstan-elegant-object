<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class ConstructorObjectsOnlyAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

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

        if ('__construct' !== $node->name->toString()) {
            return true;
        }

        return false;
    }

    /**
     * @param Node\Stmt\ClassLike $node
     *
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassMethod) {
            return [];
        }

        $classReflection = $scope->getClassReflection();

        if (null === $classReflection) {
            return [];
        }

        $errors = [];

        foreach ($node->getParams() as $param) {
            $type = $param->type;

            // No type specified
            if (null === $type) {
                $errors = array_merge($errors, RuleErrorFactory::createErrorWithTips(
                    message: 'Constructor parameter $%s has no type hint, but should be an object (Elegant Object principle).',
                    messageParameters: [$param->var->name],
                    tips: TipFactory::constructorParametersMustBeObjects()->tips(),
                )->errors());
                continue;
            }

            // Check if it's a primitive type
            $typeName = $type->toString();
            $primitiveTypes = ['int', 'float', 'string', 'bool', 'array'];

            if (in_array($typeName, $primitiveTypes, true)) {
                $errors = array_merge($errors, RuleErrorFactory::createErrorWithTips(
                    message: 'Constructor parameter $%s has primitive type %s, but should be an object (Elegant Object principle).',
                    messageParameters: [$param->var->name, $typeName],
                    tips: TipFactory::constructorParametersMustBeObjects()->tips(),
                )->errors());
            }
        }

        return $errors;
    }
}
