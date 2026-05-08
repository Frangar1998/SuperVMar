<?php

namespace SuperVMar\Product\Application\ReceiveStock;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class ReceiveStockCommand implements Command
{
    public function __construct(
        private string $idProduct,
        private int    $quantity,
    )
    {
    }

    public function idProduct(): string
    {
        return $this->idProduct;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
