<?php

namespace SuperVMar\Notification\Application\Save;

use SuperVMar\Notification\Domain\PushSubscription;
use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Notification\Domain\ValueObject\AuthKey;
use SuperVMar\Notification\Domain\ValueObject\Endpoint;
use SuperVMar\Notification\Domain\ValueObject\P256dhKey;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class PushSubscriptionCreator
{
    public function __construct(
        private PushSubscriptionRepository $repository,
    )
    {
    }

    public function create(
        Id        $id,
        Id        $idUser,
        Endpoint  $endpoint,
        AuthKey   $authKey,
        P256dhKey $p256dhKey,
    ): void
    {
        $this->repository->insert(
            PushSubscription::create(
                $id,
                $idUser,
                $endpoint,
                $authKey,
                $p256dhKey,
            )
        );
    }
}
