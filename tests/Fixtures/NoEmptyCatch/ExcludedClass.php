<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoEmptyCatch;

final class ExcludedClass
{
    public function handle(): void
    {
        try {
            $this->work();
        } catch (\RuntimeException $e) {
        }
    }

    private function work(): void
    {
    }
}
