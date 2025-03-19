<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors;

class ClassWithInterface implements SomeService
{
    public function __construct()
    {
        // new is allowed in constructor
        $date = new \DateTime();
    }

    // This is allowed because the class implements an interface that matches the pattern *Service
    public function doSomething(): \stdClass
    {
        return new \stdClass();
    }

    // This is a method not from the interface, but the class is still exempt
    // because it implements an allowed interface
    public function doSomethingElse(): \DateTime
    {
        return new \DateTime();
    }
}
