<?php

namespace SuperVMar\Notification\Application\Save;

use SuperVMar\Notification\Domain\ValueObject\AuthKey;
use SuperVMar\Notification\Domain\ValueObject\Endpoint;
use SuperVMar\Notification\Domain\ValueObject\P256dhKey;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SavePushSubscriptionCommandHandler implements CommandHandler
{
    public function __construct(
        private PushSubscriptionCreator $creator,
        private PushSubscriptionUpdater $updater,
    )
    {
    }

    public function __invoke(SavePushSubscriptionCommand $command): void
    {
        $id = new Id($command->id());
        $idUser = new Id($command->idUser());
        $endpoint = new Endpoint($command->endpoint());
        $authKey = new AuthKey($command->authKey());
        $p256dhKey = new P256dhKey($command->p256dhKey());

        try {
            $this->updater->update($idUser, $endpoint, $authKey, $p256dhKey);
        } catch (ItemNotFoundException) {
            $this->creator->create($id, $idUser, $endpoint, $authKey, $p256dhKey);
        }
    }
}
