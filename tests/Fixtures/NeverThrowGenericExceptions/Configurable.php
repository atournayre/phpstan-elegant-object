<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverThrowGenericExceptions;

final class Configurable
{
    public function reported(): void
    {
        throw new \Exception('reported');
    }

    public function allowedByException(): void
    {
        throw new \DomainException('allowed by wildcard');
    }

    public function allowedMethod(): void
    {
        throw new \Exception('allowed method');
    }

    public function notImplementing(): void
    {
        throw new PlainFailure('does not implement the interface');
    }

    public function implementing(): void
    {
        throw new SpecificFailure('implements the interface');
    }
}
