<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoEmptyCatch;

final class Configurable
{
    public function allowedException(): void
    {
        try {
            $this->work();
        } catch (\JsonException $e) {
        }
    }

    public function allowedWildcard(): void
    {
        try {
            $this->work();
        } catch (\Ignorable\Fixture\OneException $e) {
        }
    }

    public function partlyAllowed(): void
    {
        try {
            $this->work();
        } catch (\JsonException | \RuntimeException $e) {
        }
    }

    public function allowedMethod(): void
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

final class AllowedClass
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

final class ExampleTest
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
