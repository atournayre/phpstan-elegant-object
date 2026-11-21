<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\PrivateStaticLiterals;

final class TestDouble
{
    public function run(): string
    {
        return 'repeated' . 'repeated';
    }
}
