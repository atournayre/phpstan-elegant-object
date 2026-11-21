<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticProperties;

final class ExcludedClass
{
    private static int $ignored = 0;
}
