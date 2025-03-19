<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree;

use Assert\Assertion;

class ClassWithNativeAssertions
{
    private string $name;
    private int $age;

    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;

        // Assertion native PHP
        assert(!empty($name), 'Name should not be empty');
        assert($age > 0, 'Age should be positive');
    }
}
