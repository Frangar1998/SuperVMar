<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event;

use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;
use SuperVMar\Shared\Domain\Exception\DomainEventNotRegisteredException;

final readonly class DomainEventMapping
{
    private array $mapping;

    public function __construct(iterable $eventSubscribers)
    {
        $this->mapping = $this->reduce($eventSubscribers);
    }

    public function for(string $name): string
    {
        if (!isset($this->mapping[$name])) {
            throw new DomainEventNotRegisteredException($name);
        }

        return $this->mapping[$name];
    }

    private function eventsExtractor(array $events, DomainEventSubscriber $subscriber): array
    {
        return array_merge(
            $events,
            $this->reindex($subscriber)
        );
    }

    private function eventNameExtractor(string $eventClass): string
    {
        return $eventClass::eventName();
    }

    private function reduce(iterable $eventSubscribers): array
    {
        $events = [];
        foreach ($eventSubscribers as $value) {
            $events = $this->eventsExtractor($events, $value);
        }
        return $events;
    }

    private function reindex(DomainEventSubscriber $subscriber): array
    {
        $result = [];
        foreach ($subscriber::subscribedTo() as $eventClass) {
            $result[$this->eventNameExtractor($eventClass)] = $eventClass;
        }
        return $result;
    }
}