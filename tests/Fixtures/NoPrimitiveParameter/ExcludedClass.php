<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveParameter;

final class ExcludedClass
{
    public function handle(string $value): void
    {
    }
}
