<?php

namespace SuperVMar\Category\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class DeleteCategoryCommand implements Command
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