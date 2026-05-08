<?php

namespace SuperVMar\Sale\Application\SaveLine;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveSaleLineCommand implements Command
{
    public function __construct(
        private string $id,
        private array $product,
        private int $quantity
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function product(): array
    {
        return $this->product;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}