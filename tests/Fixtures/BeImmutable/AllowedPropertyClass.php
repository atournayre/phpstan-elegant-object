<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable;

class AllowedPropertyClass
{
    private readonly string $immutableProperty;

    // This should not trigger the rule - property is in the allowed list
    private string $allowedMutableProperty;

    public function __construct(string $immutableProperty, string $allowedMutableProperty)
    {
        $this->immutableProperty = $immutableProperty;
        $this->allowedMutableProperty = $allowedMutableProperty;
    }

    // This should not trigger the rule - modifies an allowed property
    public function modifyAllowedProperty(string $newValue): void
    {
        $this->allowedMutableProperty = $newValue;
    }
}
