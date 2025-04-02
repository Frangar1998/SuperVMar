<?php

namespace SuperVMar\Category\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveCategoryCommand implements Command
{
    public function __construct(
        private string $id,
        private string $name,
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
}