<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverReturnNull;

class ReturnNullClass
{
    private ?string $property = null;

    // This should trigger the rule
    public function returnNull(): ?string
    {
        return null;
    }

    // This should not trigger the rule
    public function returnString(): string
    {
        return 'string';
    }

    // This should trigger the rule
    public function returnNullWithCondition(): ?string
    {
        if ($this->property === null) {
            return null;
        }
        return $this->property;
    }

    // This should trigger the rule
    public function returnNullWithTernary(): ?string
    {
        return $this->property === 'test' ? $this->property : null;
    }

    // This should not trigger the rule - nullable return type but not returning null
    public function nullableReturnTypeButReturnsEmptyString(): ?string
    {
        return '';
    }
}
