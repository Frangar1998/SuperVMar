<?php

namespace SuperVMar\Job\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class DeleteJobCommand implements Command
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