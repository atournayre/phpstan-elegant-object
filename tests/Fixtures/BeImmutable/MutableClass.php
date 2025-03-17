<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable;

class MutableClass
{
    private readonly string $immutableProperty;

    // This should trigger the rule - property is not marked as readonly
    private string $mutableProperty;

    public function __construct(string $immutableProperty, string $mutableProperty)
    {
        $this->immutableProperty = $immutableProperty;
        $this->mutableProperty = $mutableProperty;
    }

    // This should trigger the rule - method modifies a property
    public function modifyProperty(string $newValue): void
    {
        $this->mutableProperty = $newValue;
    }

    // This should not trigger the rule - method returns a new instance
    public function withProperty(string $newValue): self
    {
        return new self($this->immutableProperty, $newValue);
    }
}
