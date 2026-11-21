<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverThrowGenericExceptions;

use InvalidArgumentException;

final class Example
{
    public function generic(): void
    {
        throw new \Exception('boom');
    }

    public function imported(): void
    {
        throw new InvalidArgumentException('bad');
    }

    public function expression(?int $value): int
    {
        return $value ?? throw new \LogicException('missing');
    }

    public function domain(): void
    {
        throw new SpecificFailure('fine');
    }

    public function rethrow(): void
    {
        try {
            $this->domain();
        } catch (SpecificFailure $e) {
            throw $e;
        }
    }

    public function wrapped(): void
    {
        try {
            $this->domain();
        } catch (SpecificFailure $e) {
            throw new SpecificFailure('wrapped', 0, $e);
        }
    }
}
