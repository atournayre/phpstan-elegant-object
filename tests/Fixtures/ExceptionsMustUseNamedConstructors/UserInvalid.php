<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExceptionsMustUseNamedConstructors;

final class UserInvalid extends \DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function becauseEmailIsMissing(): self
    {
        return new self('Email is missing');
    }

    public static function becauseNameIsMissing(): self
    {
        return new UserInvalid('Name is missing');
    }
}
