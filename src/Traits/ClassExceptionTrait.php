<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

trait ClassExceptionTrait
{
    /** @var array<string> */
    protected array $allowedClasses = [];

    public function isAllowedClassName(string $className): bool
    {
        return in_array($className, $this->allowedClasses, true);
    }
}
