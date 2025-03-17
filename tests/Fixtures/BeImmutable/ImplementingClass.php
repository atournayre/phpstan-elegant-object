<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable;

class ImplementingClass implements MutableInterface
{
    private string $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    // This should not trigger the rule due to interface implementation
    public function modifyProperty(string $newValue): void
    {
        $this->property = $newValue;
    }
}
