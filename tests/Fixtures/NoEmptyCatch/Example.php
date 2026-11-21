<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoEmptyCatch;

final class Example
{
    public function empty(): void
    {
        try {
            $this->work();
        } catch (\RuntimeException $e) {
        }
    }

    public function commentOnly(): void
    {
        try {
            $this->work();
        } catch (\RuntimeException $e) {
            // ignored
        }
    }

    public function returnsWithoutUsing(): ?int
    {
        try {
            $this->work();
        } catch (\RuntimeException | \LogicException $e) {
            return null;
        }

        return 1;
    }

    public function usesVariable(): void
    {
        try {
            $this->work();
        } catch (\RuntimeException $e) {
            error_log($e->getMessage());
        }
    }

    public function rethrows(): void
    {
        try {
            $this->work();
        } catch (\RuntimeException $e) {
            throw $e;
        }
    }

    public function throwsWithoutVariable(): void
    {
        try {
            $this->work();
        } catch (\RuntimeException) {
            throw new \LogicException('failed');
        }
    }

    public function swallowsWithoutVariable(): void
    {
        try {
            $this->work();
        } catch (\RuntimeException) {
        }
    }

    private function work(): void
    {
    }
}
