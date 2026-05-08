<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Product\Application\Save\ProductCreator;
use SuperVMar\Product\Application\Save\ProductUpdater;
use SuperVMar\Product\Application\Save\SaveProductCommand;
use SuperVMar\Product\Application\Save\SaveProductCommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;

final class SaveProductCommandHandlerTest extends ApplicationTestCase
{
    private ProductCreator $creator;
    private ProductUpdater $updater;
    private SaveProductCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createMock(ProductCreator::class);
        $this->updater = $this->createMock(ProductUpdater::class);
        $this->handler = new SaveProductCommandHandler($this->creator, $this->updater);
    }

    private function command(): SaveProductCommand
    {
        return new SaveProductCommand(
            id: self::ID_PRODUCT,
            name: 'Leche Entera',
            price: 1.29,
            ean: '1234567',
            stock: 100,
            tax: $this->taxArray(),
            category: $this->categoryArray(),
            supplier: $this->supplierArray(),
            active: 1,
            image: '',
        );
    }

    public function test_updates_existing_product_when_found(): void
    {
        $this->updater->expects($this->once())->method('update');
        $this->creator->expects($this->never())->method('create');

        ($this->handler)($this->command());
    }

    public function test_creates_product_when_updater_reports_not_found(): void
    {
        $this->updater
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new ItemNotFoundException('Product', ['id' => self::ID_PRODUCT]));

        $this->creator->expects($this->once())->method('create');

        ($this->handler)($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $this->updater->expects($this->once())->method('update');
        $this->creator->expects($this->never())->method('create');

        ($this->handler)($this->command());
    }

    public function test_accepts_command_without_image(): void
    {
        $commandNoImage = new SaveProductCommand(
            id: self::ID_PRODUCT,
            name: 'Producto Sin Imagen',
            price: 0.99,
            ean: '9876543',
            stock: 5,
            tax: $this->taxArray(),
            category: $this->categoryArray(),
            supplier: $this->supplierArray(),
            active: 1,
            image: '',
        );

        $this->updater->expects($this->once())->method('update');
        $this->creator->expects($this->never())->method('create');

        ($this->handler)($commandNoImage);
    }
}
