<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event\RabbitMQ;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\QueueEventBus;
use SuperVMar\Shared\Infrastructure\Bus\Event\DomainEventJsonSerializer;

readonly class RabbitMQEventBus implements QueueEventBus
{
    public function __construct(
        private RabbitMQConnection $connection
    )
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