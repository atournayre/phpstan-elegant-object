<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors;

class VariousConstructorPrefixes
{
    public function __construct()
    {
        // new is allowed in constructor
        $date = new \DateTime();
    }

    public static function buildFromString(string $data): self
    {
        // new is allowed in static methods starting with "build" (added to the prefixes)
        return new self();
    }

    public static function makeFromString(string $data): self
    {
        // "make" is not in the allowed prefixes list, but we're not using "new" here
        // so this is fine
        return self::buildFromString($data);
    }

    public function invalidMethod(): \DateTime
    {
        // new is NOT allowed in normal instance methods
        return new \DateTime();
    }
}
