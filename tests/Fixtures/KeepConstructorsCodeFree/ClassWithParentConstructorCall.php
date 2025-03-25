<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree;

class ParentClass
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

class ClassWithParentConstructorCall extends ParentClass
{
    private int $age;

    public function __construct(string $name, int $age)
    {
        parent::__construct($name);
        $this->age = $age;
    }
}
