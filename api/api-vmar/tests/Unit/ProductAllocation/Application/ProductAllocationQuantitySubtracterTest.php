<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\ProductAllocation\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\ProductAllocation\Application\SubtractQuantity\ProductAllocationQuantitySubtracter;
use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\Entity\Space;
use SuperVMar\ProductAllocation\Domain\Entity\Zone;
use SuperVMar\ProductAllocation\Domain\ProductAllocation;
use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\ProductAllocation\Domain\Service\ProductAllocationSearcher;
use SuperVMar\ProductAllocation\Domain\ValueObject\Coord;
use SuperVMar\ProductAllocation\Domain\ValueObject\Point;
use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\ProductAllocation\Domain\ValueObject\Spots;
use SuperVMar\Shared\Domain\Bus\Event\QueueEventBus;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final class ProductAllocationQuantitySubtracterTest extends TestCase
{
    private const string PRODUCT_ID = '550e8400-e29b-41d4-a716-000000000001';
    private const string SPACE_ID   = '550e8400-e29b-41d4-a716-000000000050';
    private const string ZONE_ID    = '550e8400-e29b-41d4-a716-000000000051';

    private ProductAllocationSearcher   $searcher;
    private ProductAllocationRepository $repository;
    private QueueEventBus               $queueEventBus;
    private ProductAllocationQuantitySubtracter $subtracter;

    protected function setUp(): void
    {
        $this->searcher      = $this->createMock(ProductAllocationSearcher::class);
        $this->repository    = $this->createMock(ProductAllocationRepository::class);
        $this->queueEventBus = $this->createMock(QueueEventBus::class);
        $this->subtracter    = new ProductAllocationQuantitySubtracter(
            $this->searcher,
            $this->repository,
            $this->queueEventBus
        );
    }

    private function buildAllocation(int $quantity): ProductAllocation
    {
        $coord = new Coord(new Point(0), new Point(0));
        $zone  = new Zone(
            new Id(self::ZONE_ID),
            new Name('Zona A'),
            $coord, $coord, $coord, $coord
        );
        $space = new Space(
            new Id(self::SPACE_ID),
            new Coord(new Point(1), new Point(2), new Point(3)),
            new Spots(10),
            $zone
        );
        $product = Product::fromArray([
            'id'    => self::PRODUCT_ID,
            'name'  => 'Leche Entera',
            'stock' => 50,
            'image' => null,
        ]);

        return new ProductAllocation($product, $space, new Quantity($quantity));
    }

    public function test_updates_allocation_after_subtract(): void
    {
        $allocation = $this->buildAllocation(10);
        $this->searcher->expects($this->once())->method('searchFirstAvailableByProduct')->willReturn($allocation);
        $this->queueEventBus->expects($this->never())->method('publish');

        $this->repository->expects($this->once())->method('update');

        $this->subtracter->subtractQuantity(
            new Id(self::PRODUCT_ID),
            new Quantity(2)
        );
    }

    public function test_publishes_low_stock_event_when_quantity_drops_below_3(): void
    {
        $allocation = $this->buildAllocation(3);
        $this->searcher->expects($this->once())->method('searchFirstAvailableByProduct')->willReturn($allocation);
        $this->repository->expects($this->once())->method('update');

        $this->queueEventBus->expects($this->once())->method('publish');

        $this->subtracter->subtractQuantity(
            new Id(self::PRODUCT_ID),
            new Quantity(1)
        );
    }

    public function test_does_not_publish_event_when_quantity_remains_above_threshold(): void
    {
        $allocation = $this->buildAllocation(10);
        $this->searcher->expects($this->once())->method('searchFirstAvailableByProduct')->willReturn($allocation);
        $this->repository->expects($this->once())->method('update');

        $this->queueEventBus->expects($this->never())->method('publish');

        $this->subtracter->subtractQuantity(
            new Id(self::PRODUCT_ID),
            new Quantity(5)
        );
    }

    public function test_searches_allocation_by_product_id(): void
    {
        $capturedId = null;
        $this->searcher
            ->expects($this->once())
            ->method('searchFirstAvailableByProduct')
            ->willReturnCallback(function ($id) use (&$capturedId) {
                $capturedId = $id;
                return $this->buildAllocation(10);
            });
        $this->repository->expects($this->once())->method('update');
        $this->queueEventBus->expects($this->never())->method('publish');

        $this->subtracter->subtractQuantity(
            new Id(self::PRODUCT_ID),
            new Quantity(1)
        );

        $this->assertSame(self::PRODUCT_ID, $capturedId->value());
    }
}
