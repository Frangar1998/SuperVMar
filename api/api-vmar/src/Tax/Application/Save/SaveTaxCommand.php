<?php

namespace SuperVMar\Tax\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveTaxCommand implements Command
{
    public function __construct(
        private string $id,
        private string $name,
        private float $percent,
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function percent(): float
    {
        return $this->percent;
    }
}