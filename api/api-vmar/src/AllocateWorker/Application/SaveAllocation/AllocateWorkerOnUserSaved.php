<?php

namespace SuperVMar\AllocateWorker\Application\SaveAllocation;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\Event\UserSavedDomainEvent;

final readonly class AllocateWorkerOnUserSaved implements DomainEventSubscriber
{
    public function __construct(
        private WorkerAllocator $workerAllocator,
    )
    {
    }

    public function __invoke(DomainEvent $event): void
    {
        $body = $event->toArray();

        $this->workerAllocator->allocate(
            new Id($event->aggregateId()),
            new Id($body['idSupermarket']),
            new Id($body['idJob'])
        );
    }

    public static function subscribedTo(): array
    {
        return [UserSavedDomainEvent::class];
    }
}