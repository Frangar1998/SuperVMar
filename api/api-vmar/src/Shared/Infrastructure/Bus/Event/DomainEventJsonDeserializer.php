<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event;

use JsonException;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Utils;

final readonly class DomainEventJsonDeserializer
{
    public function __construct(private DomainEventMapping $mapping) {}

    /**
     * @throws JsonException
     */
    public function deserialize(string $domainEvent): DomainEvent
    {
        $eventData = Utils::jsonDecode($domainEvent);
        $eventName = $eventData['data']['type'];
        $eventClass = $this->mapping->for($eventName);
        /**
         * @var $eventClass DomainEvent
         */
        return $eventClass::fromPrimitives(
            $eventData['data']['attributes']['id'],
            $eventData['data']['attributes'],
            $eventData['data']['id'],
            $eventData['data']['occurred_on']
        );
    }
}