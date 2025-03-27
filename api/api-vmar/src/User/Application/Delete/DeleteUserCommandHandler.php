<?php

namespace SuperVMar\User\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\User\Domain\ValueObject\Id;

final readonly class DeleteUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserDeleter $userDeleter,
    )
    {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $id = new Id($command->id());

        $this->userDeleter->delete($id,);
    }
}