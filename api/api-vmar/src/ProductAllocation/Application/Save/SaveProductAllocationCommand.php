<?php

namespace SuperVMar\ProductAllocation\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveProductAllocationCommand implements Command
{
    public function __construct(
        private array $product,
        private string $idSpace,
        private int $quantity
    )
    {
    }

    public function product(): array
    {
        return $this->product;
    }

    public function idSpace(): string
    {
        return $this->idSpace;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}