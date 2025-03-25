<?php

namespace SuperVMar\Shared\Domain\Bus\Event;

interface DomainEventSubscriber
{
    public function __invoke(DomainEvent $event): void;

    public static function subscribedTo(): array;
}