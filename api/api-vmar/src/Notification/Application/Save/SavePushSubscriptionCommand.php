<?php

namespace SuperVMar\Notification\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SavePushSubscriptionCommand implements Command
{
    public function __construct(
        private string $id,
        private string $idUser,
        private string $endpoint,
        private string $authKey,
        private string $p256dhKey,
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function idUser(): string
    {
        return $this->idUser;
    }

    public function endpoint(): string
    {
        return $this->endpoint;
    }

    public function authKey(): string
    {
        return $this->authKey;
    }

    public function p256dhKey(): string
    {
        return $this->p256dhKey;
    }
}
