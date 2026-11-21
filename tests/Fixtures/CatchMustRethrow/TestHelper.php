<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\CatchMustRethrow;

final class TestHelper
{
    public function handle(): void
    {
        try {
            $this->run();
        } catch (\Throwable $e) {
        }
    }

    private function run(): void
    {
    }
}
