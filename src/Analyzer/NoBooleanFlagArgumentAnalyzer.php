<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class NoBooleanFlagArgumentAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /**
     * @param array<string> $excludedPaths
     */
    public function __construct(array $excludedPaths = [])
    {
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

        if ($scope->getClassReflection() === null) {
            return true;
        }

        return $this->isExcluded($scope->getFile());
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

        $errors = [];

        foreach ($node->getParams() as $param) {
            if ($param->default === null || !$this->containsBool($param->type)) {
                continue;
            }

            $errors[] = RuleErrorFactory::createErrorWithTips(
                'Method %s::%s() defines boolean flag parameter $%s. Split the behavior into explicit methods or use a dedicated value object/enum.',
                'elegantObject.methods.booleanFlagArgument',
                [
                    $classReflection->getName(),
                    $node->name->toString(),
                    $this->parameterName($param),
                ],
            )->errors()[0];
        }

        return $errors;
    }

    private function containsBool(Identifier|Name|ComplexType|null $type): bool
    {
        if ($type instanceof NullableType) {
            return $this->containsBool($type->type);
        }

        if ($type instanceof UnionType) {
            foreach ($type->types as $inner) {
                if ($this->containsBool($inner)) {
                    return true;
                }
            }

            return false;
        }

        return $type instanceof Identifier && $type->toLowerString() === 'bool';
    }

    private function parameterName(Node\Param $param): string
    {
        if ($param->var instanceof Variable && is_string($param->var->name)) {
            return $param->var->name;
        }

        return 'inconnu';
    }

    private function isExcluded(string $file): bool
    {
        if ($this->isExcludedPath($file)) {
            return true;
        }

        foreach ($this->excludedPaths as $excludedPath) {
            if ($excludedPath !== '' && str_contains($file, $excludedPath)) {
                return true;
            }
        }

        return false;
    }
}
