<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoNullUsage;

final class ExcludedClass
{
    private ?string $value = null;
}
