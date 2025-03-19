<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors;

class InvalidClass
{
    public function __construct()
    {
        // new is allowed in constructor
        $date = new \DateTime();
    }

    public function normalMethod(): \DateTime
    {
        // new is NOT allowed in normal instance methods
        return new \DateTime();
    }

    public function anotherMethod(): \stdClass
    {
        // new is NOT allowed in normal instance methods
        return new \stdClass();
    }

    public static function getObject(): self
    {
        // this static method doesn't start with a valid prefix
        // but we're not using "new" here, so it's fine
        return self::newInstance();
    }

    public static function newInstance(): self
    {
        // new is allowed in static methods starting with "new"
        return new self();
    }
}
