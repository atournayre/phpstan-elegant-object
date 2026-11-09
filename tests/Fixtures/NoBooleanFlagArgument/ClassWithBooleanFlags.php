<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoBooleanFlagArgument;

class ClassWithBooleanFlags
{
    public function plain(bool $flag = false): void
    {
    }

    public function nullable(?bool $flag = null): void
    {
    }

    public function union(bool|int $flag = 0): void
    {
    }

    public function noDefault(bool $flag): void
    {
    }

    public function untyped($flag = false): void
    {
    }

    public function other(string $name = 'a', int $count = 1): void
    {
    }
}
