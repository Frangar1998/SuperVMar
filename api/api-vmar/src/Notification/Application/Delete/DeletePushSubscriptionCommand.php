<?php

namespace SuperVMar\Notification\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class DeletePushSubscriptionCommand implements Command
{
    public function __construct(
        private string $idUser,
    )
    {
    }

    public function idUser(): string
    {
        return $this->idUser;
    }
}
