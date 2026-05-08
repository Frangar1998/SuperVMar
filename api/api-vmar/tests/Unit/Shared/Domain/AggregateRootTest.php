<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Shared\Domain;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

/**
 * Concrete stub for testing the abstract AggregateRoot.
 */
final class ConcreteAggregate extends AggregateRoot
{
    public function recordEvent(DomainEvent $event): void
    {
        $this->record($event);
    }
}

/**
 * Minimal stub domain event for testing.
 */
final class StubDomainEvent extends DomainEvent
{
    public static function eventName(): string
    {
        return 'test.stub.event';
    }

    public function toArray(): array
    {
        return [];
    }

    public static function fromPrimitives(
        string $aggregateId,
        array  $body,
        string $eventId,
        string $occurredOn,
    ): self {
        return new self($aggregateId, $eventId, $occurredOn);
    }
}

final class AggregateRootTest extends TestCase
{
    public function test_new_aggregate_has_no_pending_events(): void
    {
        $aggregate = new ConcreteAggregate();

        $this->assertSame([], $aggregate->pullDomainEvents());
    }

    public function test_record_adds_event_to_queue(): void
    {
        $aggregate = new ConcreteAggregate();
        $event     = new StubDomainEvent('agg-id-001');

        $aggregate->recordEvent($event);

        $events = $aggregate->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertSame($event, $events[0]);
    }

    public function test_pull_domain_events_clears_the_queue(): void
    {
        $aggregate = new ConcreteAggregate();
        $aggregate->recordEvent(new StubDomainEvent('agg-id-001'));

        $aggregate->pullDomainEvents(); // first pull
        $second = $aggregate->pullDomainEvents();

        $this->assertSame([], $second);
    }

    public function test_multiple_events_are_returned_in_order(): void
    {
        $aggregate = new ConcreteAggregate();
        $first     = new StubDomainEvent('agg-id-001');
        $second    = new StubDomainEvent('agg-id-002');

        $aggregate->recordEvent($first);
        $aggregate->recordEvent($second);

        $events = $aggregate->pullDomainEvents();

        $this->assertCount(2, $events);
        $this->assertSame($first, $events[0]);
        $this->assertSame($second, $events[1]);
    }
}
