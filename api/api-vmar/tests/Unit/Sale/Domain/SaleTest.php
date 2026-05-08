<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Sale\Domain;

use PHPUnit\Framework\TestCase;
use SuperVMar\Sale\Domain\Event\SaleFinishedDomainEvent;
use SuperVMar\Sale\Domain\Sale;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class SaleTest extends TestCase
{
    private const string SALE_UUID = '550e8400-e29b-41d4-a716-000000000001';

    public function test_create_initialises_sale_with_zero_amounts(): void
    {
        $sale = Sale::create(new Id(self::SALE_UUID));

        $this->assertSame(0.0, $sale->amount()->value());
        $this->assertSame(0.0, $sale->taxesAmount()->value());
        $this->assertSame(0.0, $sale->totalAmount()->value());
    }

    public function test_create_assigns_provided_id(): void
    {
        $id   = new Id(self::SALE_UUID);
        $sale = Sale::create($id);

        $this->assertSame(self::SALE_UUID, $sale->id()->value());
    }

    public function test_new_sale_has_no_pending_domain_events(): void
    {
        $sale   = Sale::create(new Id(self::SALE_UUID));
        $events = $sale->pullDomainEvents();

        $this->assertSame([], $events);
    }

    public function test_set_finished_date_records_sale_finished_event(): void
    {
        $sale = Sale::create(new Id(self::SALE_UUID));

        $sale->setFinishedDate();

        $events = $sale->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(SaleFinishedDomainEvent::class, $events[0]);
    }

    public function test_sale_finished_event_has_correct_aggregate_id(): void
    {
        $sale = Sale::create(new Id(self::SALE_UUID));

        $sale->setFinishedDate();

        $events = $sale->pullDomainEvents();
        $this->assertSame(self::SALE_UUID, $events[0]->aggregateId());
    }

    public function test_finished_date_is_set_after_calling_set_finished_date(): void
    {
        $sale = Sale::create(new Id(self::SALE_UUID));

        $this->assertNull($sale->finishedDate());

        $sale->setFinishedDate();

        $this->assertNotNull($sale->finishedDate());
    }
}
