<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoNullUsage;

final class TolerantClass implements TolerantInterface
{
    private ?string $value = null;
}
