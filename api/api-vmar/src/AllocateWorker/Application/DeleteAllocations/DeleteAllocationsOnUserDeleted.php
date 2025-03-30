<?php

namespace SuperVMar\AllocateWorker\Application\DeleteAllocations;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\Event\UserDeletedDomainEvent;

final readonly class DeleteAllocationsOnUserDeleted implements DomainEventSubscriber
{
    public function __construct(
        private WorkerAllocationsDeleter $workerAllocationsDeleter,
    )
    {
    }

    public function __invoke(DomainEvent $event): void
    {
        $idUser = new Id($event->aggregateId());
        $this->workerAllocationsDeleter->deleteAllocationsFrom($idUser);
    }

    public static function subscribedTo(): array
    {
        return [UserDeletedDomainEvent::class];
    }
}