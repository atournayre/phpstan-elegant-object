<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExceptionsMustUseNamedConstructors;

final class Thrower
{
    public function good(): void
    {
        throw UserInvalid::becauseEmailIsMissing();
    }

    public function newDomain(): void
    {
        throw new UserInvalid('x');
    }

    public function newBuiltIn(): void
    {
        throw new \InvalidArgumentException('not flagged: built-in');
    }

    public function newLoose(): void
    {
        throw new LooseException('x');
    }

    public function notAnException(): \stdClass
    {
        return new \stdClass();
    }

    public function tolerated(): void
    {
        new AllowedByNameException();
        new AllowedByInterfaceException();
    }
}
