<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Product\Application\SubtractStock\ProductStockSubtracter;
use SuperVMar\Product\Application\SubtractStock\SubtractStockOnSaleFinished;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Sale\Domain\Event\SaleFinishedDomainEvent;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class SubtractStockOnSaleFinishedTest extends TestCase
{
    private const string SALE_ID    = '550e8400-e29b-41d4-a716-000000000020';
    private const string PRODUCT_ID = '550e8400-e29b-41d4-a716-000000000001';

    private ProductStockSubtracter $subtracter;
    private SubtractStockOnSaleFinished $subscriber;

    protected function setUp(): void
    {
        $this->subtracter = $this->createStub(ProductStockSubtracter::class);
        $this->subscriber = new SubtractStockOnSaleFinished($this->subtracter);
    }

    private function buildEvent(array $lines): SaleFinishedDomainEvent
    {
        return new SaleFinishedDomainEvent(
            aggregateId: self::SALE_ID,
            lines: $lines,
        );
    }

    public function test_subtracts_stock_for_each_line(): void
    {
        $lines = [
            ['product' => ['id' => self::PRODUCT_ID], 'quantity' => 2],
            ['product' => ['id' => '550e8400-e29b-41d4-a716-000000000002'], 'quantity' => 3],
        ];

        $subtracter = $this->createMock(ProductStockSubtracter::class);
        $subtracter->expects($this->exactly(2))->method('subtractStock');
        $subscriber = new SubtractStockOnSaleFinished($subtracter);

        $subscriber($this->buildEvent($lines));
    }

    public function test_calls_subtract_stock_with_correct_product_id_and_quantity(): void
    {
        $capturedCalls = [];
        $subtracter = $this->createMock(ProductStockSubtracter::class);
        $subtracter
            ->expects($this->once())
            ->method('subtractStock')
            ->willReturnCallback(function (Id $id, Stock $stock) use (&$capturedCalls) {
                $capturedCalls[] = [$id->value(), $stock->value()];
            });
        $subscriber = new SubtractStockOnSaleFinished($subtracter);

        $subscriber($this->buildEvent([
            ['product' => ['id' => self::PRODUCT_ID], 'quantity' => 5],
        ]));

        $this->assertSame([[self::PRODUCT_ID, 5]], $capturedCalls);
    }

    public function test_does_nothing_when_no_lines(): void
    {
        $subtracter = $this->createMock(ProductStockSubtracter::class);
        $subtracter->expects($this->never())->method('subtractStock');
        $subscriber = new SubtractStockOnSaleFinished($subtracter);

        $subscriber($this->buildEvent([]));
    }

    public function test_subscribed_to_sale_finished_event(): void
    {
        $this->assertContains(
            SaleFinishedDomainEvent::class,
            SubtractStockOnSaleFinished::subscribedTo()
        );
    }
}
