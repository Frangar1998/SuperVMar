<?php

namespace SuperVMar\Sale\Application\FinishSale;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class FinishSaleCommand implements Command
{
    public function __construct(
        private string $id,
        private string $payMethod
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function payMethod(): string
    {
        return $this->payMethod;
    }
}