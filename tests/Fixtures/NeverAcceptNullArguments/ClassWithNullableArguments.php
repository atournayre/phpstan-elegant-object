<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments;

class ClassWithNullableArguments
{
    public function methodWithNullableArgument(?string $param): void
    {
        // Cette méthode accepte un paramètre nullable
    }

    public function methodWithMultipleParams(string $normalParam, ?int $nullableParam): void
    {
        // Cette méthode a un paramètre normal et un paramètre nullable
    }

    public function methodWithoutNullableParams(string $param1, int $param2): void
    {
        // Cette méthode n'accepte pas de paramètres nullable
    }
}
