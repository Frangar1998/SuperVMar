<?php

namespace SuperVMar\Notification\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeletePushSubscriptionCommandHandler implements CommandHandler
{
    public function __construct(
        private PushSubscriptionDeleter $deleter,
    )
    {
    }

    public function __invoke(DeletePushSubscriptionCommand $command): void
    {
        $this->deleter->delete(
            new Id($command->idUser()),
        );
    }
}
