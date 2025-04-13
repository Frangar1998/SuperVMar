<?php

namespace SuperVMar\ProductAllocation\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class DeleteProductAllocationCommand implements Command
{
    public function __construct(
        private string $idSpace
    )
    {
    }

    public function idSpace(): string
    {
        return $this->idSpace;
    }
}