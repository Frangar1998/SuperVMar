<?php

namespace SuperVMar\Shared\Domain;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

abstract class AggregateRoot
{
    /** @var array<mixed, DomainEvent>  */
    private array $unpublishedEvents = [];
    final protected function record(DomainEvent $event): void
    {
        $this->unpublishedEvents[] = $event;
    }

    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->unpublishedEvents;
        $this->unpublishedEvents = [];
        return $domainEvents;
    }
}