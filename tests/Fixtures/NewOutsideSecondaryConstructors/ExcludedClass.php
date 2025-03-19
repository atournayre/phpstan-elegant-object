<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures;

class ExcludedClass
{
    public function __construct()
    {
        // new is allowed in constructor
        $date = new \DateTime();
    }

    // This class is in the excluded list, so "new" is allowed anywhere
    public function normalMethod(): \DateTime
    {
        return new \DateTime();
    }

    public function anotherMethod(): \stdClass
    {
        return new \stdClass();
    }
}
