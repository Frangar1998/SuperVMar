<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Notification\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Notification\Application\Send\NotifyRestockersOnLowStock;
use SuperVMar\Notification\Application\Send\RestockerNotifier;
use SuperVMar\ProductAllocation\Domain\Event\LowStockDetectedDomainEvent;

final class NotifyRestockersOnLowStockTest extends TestCase
{
    private const string AGGREGATE_ID  = '550e8400-e29b-41d4-a716-000000000050';
    private const string PRODUCT_ID    = '550e8400-e29b-41d4-a716-000000000001';
    private const string SPACE_ID      = '550e8400-e29b-41d4-a716-000000000051';
    private const string ZONE_ID       = '550e8400-e29b-41d4-a716-000000000052';
    private const string PRODUCT_NAME  = 'Leche Entera';
    private const string ZONE_NAME     = 'Zona A';

    private RestockerNotifier $notifier;
    private NotifyRestockersOnLowStock $subscriber;

    protected function setUp(): void
    {
        $this->notifier    = $this->createStub(RestockerNotifier::class);
        $this->subscriber  = new NotifyRestockersOnLowStock($this->notifier);
    }

    private function buildEvent(int $quantity = 2): LowStockDetectedDomainEvent
    {
        return new LowStockDetectedDomainEvent(
            aggregateId:   self::AGGREGATE_ID,
            idProduct:     self::PRODUCT_ID,
            productName:   self::PRODUCT_NAME,
            idSpace:       self::SPACE_ID,
            spacePosition: '(1,2)',
            idZone:        self::ZONE_ID,
            zoneName:      self::ZONE_NAME,
            quantity:      $quantity,
            maxSpots:      10,
        );
    }

    public function test_notifies_restockers_with_event_data(): void
    {
        $notifier = $this->createMock(RestockerNotifier::class);
        $notifier
            ->expects($this->once())
            ->method('notify')
            ->with(
                self::ZONE_ID,
                self::PRODUCT_NAME,
                self::ZONE_NAME,
                2,
            );

        (new NotifyRestockersOnLowStock($notifier))($this->buildEvent(2));
    }

    public function test_notifies_with_zero_quantity(): void
    {
        $notifier = $this->createMock(RestockerNotifier::class);
        $notifier
            ->expects($this->once())
            ->method('notify')
            ->with(
                self::ZONE_ID,
                self::PRODUCT_NAME,
                self::ZONE_NAME,
                0,
            );

        (new NotifyRestockersOnLowStock($notifier))($this->buildEvent(0));
    }

    public function test_subscribed_to_low_stock_event(): void
    {
        $this->assertContains(
            LowStockDetectedDomainEvent::class,
            NotifyRestockersOnLowStock::subscribedTo()
        );
    }
}
