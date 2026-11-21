<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExceptionsMustUseNamedConstructors;

final class TestedThrower
{
    public function skipped(): void
    {
        throw new \InvalidArgumentException('test classes are skipped');
    }
}
