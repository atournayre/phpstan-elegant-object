<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

trait ParameterExceptionTrait
{
    /**
     * @var array<string>
     */
    protected array $allowedParameterNames = [];

    /**
     * Check if parameter name is in the allowed list.
     */
    protected function isAllowedParameterName(string $parameterName): bool
    {
        return in_array($parameterName, $this->allowedParameterNames, true);
    }
}
