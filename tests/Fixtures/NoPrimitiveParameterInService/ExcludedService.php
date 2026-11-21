<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveParameterInService;

final class ExcludedService
{
    public function handle(string $value): void
    {
    }
}
