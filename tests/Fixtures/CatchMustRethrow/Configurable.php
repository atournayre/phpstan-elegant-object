<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\CatchMustRethrow;

final class Configurable
{
    public function reported(): void
    {
        try {
            $this->run();
        } catch (\Exception $e) {
        }
    }

    public function allowedMethod(): void
    {
        try {
            $this->run();
        } catch (\Exception $e) {
        }
    }

    public function allowedException(): void
    {
        try {
            $this->run();
        } catch (\JsonException $e) {
        }
    }

    private function run(): void
    {
    }
}

final class AllowedByWildcardClass
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

interface TolerantInterface
{
}

final class WithTolerantInterface implements TolerantInterface
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
