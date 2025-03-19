<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors;

interface SomeService
{
    public function doSomething(): \stdClass;
}
