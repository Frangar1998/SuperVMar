<?php

namespace SuperVMar\Notification\Application\Delete;

use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class PushSubscriptionDeleter
{
    public function __construct(
        private PushSubscriptionRepository $repository,
    )
    {
    }

    public function delete(Id $idUser): void
    {
        $this->repository->deleteByUserId($idUser);
    }
}
