<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticProperties;

final class Configurable
{
    private static int $reported = 0;
    private static int $allowedName = 0;
}
