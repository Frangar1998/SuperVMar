<?php

namespace SuperVMar\ProductAllocation\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveProductAllocationCommand implements Command
{
    public function __construct(
        private string $idSpace,
        private string $product,
        private int $quantity
    )
    {
    }

    public function idSpace(): string
    {
        return $this->idSpace;
    }

    public function product(): string
    {
        return $this->product;
    }


    public function quantity(): int
    {
        return $this->quantity;
    }
}