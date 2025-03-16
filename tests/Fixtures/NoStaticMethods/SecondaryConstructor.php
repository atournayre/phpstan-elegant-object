<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticMethods;

final readonly class SecondaryConstructor
{
    public static function create(): self
    {
        return new self();
    }
}
