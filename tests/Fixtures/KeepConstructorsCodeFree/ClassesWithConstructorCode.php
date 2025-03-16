<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree;

class ClassWithLogicInConstructor
{
    private string $name;
    private int $age;

    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        // A conversion is an example of logic that should not be in a constructor
        $this->age = $age < 0 ? 0 : $age;

        // This method call should not be in a constructor
        $this->initialize();
    }

    private function initialize(): void
    {
    }
}

class AnotherClassWithLogicInConstructor
{
    private string $message;

    public function __construct(string $input)
    {
        // This method call should not be in a constructor
        $this->message = strtoupper($input) . ' - processed';
    }
}
