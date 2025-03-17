<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable;

class ImmutableClass
{
    private readonly string $property1;
    private readonly array $property2;

    public function __construct(string $property1, array $property2)
    {
        $this->property1 = $property1;
        $this->property2 = $property2;
    }

    public function getProperty1(): string
    {
        return $this->property1;
    }

    public function getProperty2(): array
    {
        return $this->property2;
    }

    // This should not trigger the rule - method creates a new instance
    public function withProperty1(string $newValue): self
    {
        return new self($newValue, $this->property2);
    }

    // This should not trigger the rule - method creates a new instance
    public function withProperty2(array $newValue): self
    {
        return new self($this->property1, $newValue);
    }
}
