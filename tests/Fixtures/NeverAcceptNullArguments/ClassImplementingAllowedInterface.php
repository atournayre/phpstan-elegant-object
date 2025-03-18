<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments;

class ClassImplementingAllowedInterface implements AllowedInterface
{
    public function interfaceMethod(?string $param)
    {
        // Cette méthode provient d'une interface autorisée
    }

    public function anotherInterfaceMethod($param = null)
    {
        // Cette méthode provient aussi d'une interface autorisée
    }

    public function normalMethod(?string $param): void
    {
        // Cette méthode a un paramètre nullable mais n'est pas d'une interface
        // Elle serait normalement détectée, mais comme la classe implémente une interface
        // autorisée, elle est ignorée.
    }
}