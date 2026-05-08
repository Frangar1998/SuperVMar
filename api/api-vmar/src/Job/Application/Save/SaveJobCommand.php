<?php

namespace SuperVMar\Job\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveJobCommand implements Command
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