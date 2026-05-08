<?php

namespace SuperVMar\Shared\Domain\Bus\Event;

interface QueueEventBus
{
    public function publish(DomainEvent ...$events): void;
}