<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Sale\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Sale\Application\Delete\DeleteSaleCommand;
use SuperVMar\Sale\Application\Delete\DeleteSaleCommandHandler;
use SuperVMar\Sale\Application\Delete\SaleDeleter;

final class DeleteSaleCommandHandlerTest extends ApplicationTestCase
{
    private SaleDeleter $deleter;
    private DeleteSaleCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(SaleDeleter::class);
        $this->handler = new DeleteSaleCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_sale_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeleteSaleCommand(self::ID_SALE));
    }

    public function test_calls_deleter_with_the_correct_id(): void
    {
        $capturedId = null;

        $this->deleter
            ->expects($this->once())
            ->method('delete')
            ->willReturnCallback(function ($id) use (&$capturedId) {
                $capturedId = $id;
            });

        ($this->handler)(new DeleteSaleCommand(self::ID_SALE));

        $this->assertSame(self::ID_SALE, $capturedId->value());
    }
}
