<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\CatchMustRethrow;

final class Example
{
    public function bareRethrow(): void
    {
        try {
            $this->run();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function chainedNamed(): void
    {
        try {
            $this->run();
        } catch (\Exception $e) {
            throw new \DomainException('failed', previous: $e);
        }
    }

    public function chainedPositional(): void
    {
        try {
            $this->run();
        } catch (\Exception $e) {
            throw new \DomainException('failed', 0, $e);
        }
    }

    public function swallowed(): void
    {
        try {
            $this->run();
        } catch (\Exception $e) {
            $this->run();
        }
    }

    public function empty(): void
    {
        try {
            $this->run();
        } catch (\InvalidArgumentException | \JsonException $e) {
        }
    }

    public function throwsWithoutChaining(): void
    {
        try {
            $this->run();
        } catch (\Exception $e) {
            throw new \DomainException('failed');
        }
    }

    public function withoutVariable(): void
    {
        try {
            $this->run();
        } catch (\Exception) {
            throw new \DomainException('failed');
        }
    }

    private function run(): void
    {
    }
}
