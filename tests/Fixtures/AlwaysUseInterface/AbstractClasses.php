<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AlwaysUseInterface;

abstract class AbstractEntity
{
    abstract public function getId(): string;
}

class ConcreteEntity extends AbstractEntity implements EntityInterface
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

interface EntityInterface
{
    public function getId(): string;
}
