<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\ProductAllocation\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\ProductAllocation\Application\Save\ProductAllocationCreator;
use SuperVMar\ProductAllocation\Application\Save\ProductAllocationUpdater;
use SuperVMar\ProductAllocation\Application\Save\SaveProductAllocationCommand;
use SuperVMar\ProductAllocation\Application\Save\SaveProductAllocationCommandHandler;
use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\Service\ProductSearcher;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;

final class SaveProductAllocationCommandHandlerTest extends TestCase
{
    private const string SPACE_ID   = '550e8400-e29b-41d4-a716-000000000050';
    private const string PRODUCT_ID = '550e8400-e29b-41d4-a716-000000000001';

    private ProductAllocationCreator $creator;
    private ProductAllocationUpdater $updater;
    private ProductSearcher          $productSearcher;
    private SaveProductAllocationCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator         = $this->createStub(ProductAllocationCreator::class);
        $this->updater         = $this->createStub(ProductAllocationUpdater::class);
        $this->productSearcher = $this->createStub(ProductSearcher::class);
        $this->handler = new SaveProductAllocationCommandHandler(
            $this->creator,
            $this->updater,
            $this->productSearcher
        );
    }

    private function command(): SaveProductAllocationCommand
    {
        return new SaveProductAllocationCommand(self::SPACE_ID, self::PRODUCT_ID, 5);
    }

    private function buildProductEntity(): Product
    {
        return Product::fromArray([
            'id'    => self::PRODUCT_ID,
            'name'  => 'Leche Entera',
            'stock' => 50,
            'image' => null,
        ]);
    }

    public function test_updates_existing_allocation_when_found(): void
    {
        $productSearcher = $this->createStub(ProductSearcher::class);
        $productSearcher->method('search')->willReturn($this->buildProductEntity());

        $updater = $this->createMock(ProductAllocationUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(ProductAllocationCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveProductAllocationCommandHandler($creator, $updater, $productSearcher))($this->command());
    }

    public function test_creates_allocation_when_updater_reports_not_found(): void
    {
        $productSearcher = $this->createStub(ProductSearcher::class);
        $productSearcher->method('search')->willReturn($this->buildProductEntity());

        $updater = $this->createMock(ProductAllocationUpdater::class);
        $updater->expects($this->once())->method('update')->willThrowException(
            new ItemNotFoundException('ProductAllocation', ['idSpace' => self::SPACE_ID])
        );
        $creator = $this->createMock(ProductAllocationCreator::class);
        $creator->expects($this->once())->method('create');

        (new SaveProductAllocationCommandHandler($creator, $updater, $productSearcher))($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $productSearcher = $this->createStub(ProductSearcher::class);
        $productSearcher->method('search')->willReturn($this->buildProductEntity());

        $updater = $this->createMock(ProductAllocationUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(ProductAllocationCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveProductAllocationCommandHandler($creator, $updater, $productSearcher))($this->command());
    }

    public function test_searches_product_by_id_from_command(): void
    {
        $capturedId = null;
        $productSearcher = $this->createMock(ProductSearcher::class);
        $productSearcher
            ->expects($this->once())
            ->method('search')
            ->willReturnCallback(function ($id) use (&$capturedId) {
                $capturedId = $id;
                return $this->buildProductEntity();
            });

        (new SaveProductAllocationCommandHandler(
            $this->createStub(ProductAllocationCreator::class),
            $this->createStub(ProductAllocationUpdater::class),
            $productSearcher
        ))($this->command());

        $this->assertSame(self::PRODUCT_ID, $capturedId->value());
    }
}
