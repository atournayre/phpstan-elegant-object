<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoBooleanFlagArgument;

class ExcludedClass
{
    public function plain(bool $flag = false): void
    {
    }
}
