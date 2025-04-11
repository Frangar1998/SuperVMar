<?php

namespace SuperVMar\Product\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class DeleteProductCommand implements Command
{
    public function __construct(
        private string $id
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }
}