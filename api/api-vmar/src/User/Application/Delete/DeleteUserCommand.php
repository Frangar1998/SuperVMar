<?php

namespace SuperVMar\User\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class DeleteUserCommand implements Command
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