<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event;

use JsonException;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Utils;

final readonly class DomainEventJsonSerializer
{
    /**
     * @throws JsonException
     */
    public static function serialize(DomainEvent $domainEvent): string
    {
        return Utils::jsonEncode(
            [
                'data' => [
                    'id' => $domainEvent->eventId(),
                    'type' => $domainEvent::eventName(),
                    'occurred_on' => $domainEvent->occurredOn(),
                    'attributes' => array_merge($domainEvent->toArray(), ['id' => $domainEvent->aggregateId()]),
                ],
                'meta' => [],
            ]
        );
    }
}