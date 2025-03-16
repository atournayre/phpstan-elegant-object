<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

trait MethodExceptionTrait
{
    /** @var array<string> */
    protected array $allowedMethodNames = [];

    public function isAllowedMethodName(string $methodName): bool
    {
        return in_array($methodName, $this->allowedMethodNames, true);
    }
}
