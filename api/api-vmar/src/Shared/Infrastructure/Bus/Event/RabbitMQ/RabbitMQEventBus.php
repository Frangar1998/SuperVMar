<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event\RabbitMQ;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Infrastructure\Bus\Event\DomainEventJsonSerializer;

final readonly class RabbitMQEventBus
{
    public function __construct(private RabbitMqConnection $connection)
    {
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->publishEvent($event);
        }
    }

    private function publishEvent(DomainEvent $event): void
    {
        $body = DomainEventJsonSerializer::serialize($event);
        $routingKey = $event->eventName();
        $this->connection->publish($body, $routingKey);
    }
}