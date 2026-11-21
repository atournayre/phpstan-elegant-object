<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\PrivateStaticLiterals;

final class ExcludedClass
{
    public function run(): string
    {
        return 'repeated' . 'repeated';
    }
}
