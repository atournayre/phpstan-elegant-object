<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticProperties;

final class AllowedByInterface implements TolerantInterface
{
    private static int $tolerated = 0;
}
