<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoGenericCatch;

final class Example
{
    public function generic(): void
    {
        try {
            $this->run();
        } catch (\Exception $e) {
        }
    }

    public function multi(): void
    {
        try {
            $this->run();
        } catch (\InvalidArgumentException | \Throwable $e) {
        }
    }

    public function others(): void
    {
        try {
            $this->run();
        } catch (\Error | \RuntimeException | \LogicException $e) {
        }
    }

    public function rethrow(): void
    {
        try {
            $this->run();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function specific(): void
    {
        try {
            $this->run();
        } catch (\InvalidArgumentException | \JsonException $e) {
        }
    }

    private function run(): void
    {
    }
}
