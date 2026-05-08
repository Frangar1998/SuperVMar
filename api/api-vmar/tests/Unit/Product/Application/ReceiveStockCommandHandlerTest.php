<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Product\Application\ReceiveStock\ReceiveStockCommand;
use SuperVMar\Product\Application\ReceiveStock\ReceiveStockCommandHandler;
use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Service\ProductSearcher;

final class ReceiveStockCommandHandlerTest extends ApplicationTestCase
{
    private ProductSearcher $searcher;
    private ProductRepository $repository;
    private ReceiveStockCommandHandler $handler;

    protected function setUp(): void
    {
        $this->searcher    = $this->createMock(ProductSearcher::class);
        $this->repository  = $this->createMock(ProductRepository::class);
        $this->handler     = new ReceiveStockCommandHandler($this->searcher, $this->repository);
    }

    public function test_increases_product_stock(): void
    {
        $product = $this->buildProduct(stock: 50);

        $this->searcher
            ->expects($this->once())
            ->method('search')
            ->willReturn($product);

        $this->repository->expects($this->once())->method('updateStock');

        ($this->handler)(new ReceiveStockCommand(self::ID_PRODUCT, 10));
    }

    public function test_stock_is_correctly_added_before_update(): void
    {
        $product = $this->buildProduct(stock: 50);

        $this->searcher->expects($this->once())->method('search')->willReturn($product);

        $capturedStock = null;
        $this->repository
            ->expects($this->once())
            ->method('updateStock')
            ->willReturnCallback(function ($id, $stock) use (&$capturedStock) {
                $capturedStock = $stock;
            });

        ($this->handler)(new ReceiveStockCommand(self::ID_PRODUCT, 10));

        $this->assertSame(60, $capturedStock->value());
    }
}
