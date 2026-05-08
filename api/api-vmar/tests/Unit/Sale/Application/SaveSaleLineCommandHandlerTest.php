<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Sale\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Sale\Application\SaveLine\SaleCreator;
use SuperVMar\Sale\Application\SaveLine\SaleUpdater;
use SuperVMar\Sale\Application\SaveLine\SaveSaleLineCommand;
use SuperVMar\Sale\Application\SaveLine\SaveSaleLineCommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;

final class SaveSaleLineCommandHandlerTest extends ApplicationTestCase
{
    private SaleCreator $creator;
    private SaleUpdater $updater;
    private SaveSaleLineCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createMock(SaleCreator::class);
        $this->updater = $this->createMock(SaleUpdater::class);
        $this->handler = new SaveSaleLineCommandHandler($this->creator, $this->updater);
    }

    private function command(): SaveSaleLineCommand
    {
        return new SaveSaleLineCommand(
            id: self::ID_SALE,
            product: $this->saleProductArray(),
            quantity: 2,
        );
    }

    public function test_updates_existing_sale_when_found(): void
    {
        $this->updater->expects($this->once())->method('update');
        $this->creator->expects($this->never())->method('create');

        ($this->handler)($this->command());
    }

    public function test_creates_new_sale_when_updater_reports_not_found(): void
    {
        $this->updater
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new ItemNotFoundException('Sale', ['id' => self::ID_SALE]));

        $this->creator->expects($this->once())->method('create');

        ($this->handler)($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $this->updater->expects($this->once())->method('update');
        $this->creator->expects($this->never())->method('create');

        ($this->handler)($this->command());
    }
}
