<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

trait PropertyExceptionTrait
{
    /** @var array<string> */
    protected array $allowedPropertiesNames = [];

    public function isAllowedPropertyName(string $propertyName): bool
    {
        return in_array($propertyName, $this->allowedPropertiesNames, true);
    }
}
