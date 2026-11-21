<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\PrivateStaticLiterals;

final class Configurable
{
    public function run(): string
    {
        return 'allowed' . 'allowed' . 'allowed' . 'ab' . 'ab' . 'ab' . 'once' . 'twice' . 'twice' . 'thrice' . 'thrice' . 'thrice';
    }
}
