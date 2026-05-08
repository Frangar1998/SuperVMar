<?php

namespace SuperVMar\Notification\Application\Save;

use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Notification\Domain\Service\PushSubscriptionSearcher;
use SuperVMar\Notification\Domain\ValueObject\AuthKey;
use SuperVMar\Notification\Domain\ValueObject\Endpoint;
use SuperVMar\Notification\Domain\ValueObject\P256dhKey;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class PushSubscriptionUpdater
{
    public function __construct(
        private PushSubscriptionSearcher $pushSubscriptionSearcher,
        private PushSubscriptionRepository $pushSubscriptionRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id        $idUser,
        Endpoint  $endpoint,
        AuthKey   $authKey,
        P256dhKey $p256dhKey,
    ): void
    {
        $pushSubscription = $this->pushSubscriptionSearcher->searchByUserId($idUser);
        $pushSubscription->changeEndpoint($endpoint);
        $pushSubscription->changeAuthKey($authKey);
        $pushSubscription->changeP256dhKey($p256dhKey);
        $this->pushSubscriptionRepository->update($pushSubscription);
    }
}
