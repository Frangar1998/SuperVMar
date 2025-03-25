<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event;

use SuperVMar\Shared\Infrastructure\Bus\HandlerBuilder;
use Traversable;

class DomainEventSubscriberLocator
{
    private array $mapping;

    public function __construct(Traversable $mapping)
    {
        $this->mapping = iterator_to_array($mapping);
    }

    public function allSubscribedTo(string $eventClass): array
    {
        $formatted = HandlerBuilder::forPipedCallables($this->mapping);

        return $formatted[$eventClass];
    }

    public function all(): array
    {
        return $this->mapping;
    }
}