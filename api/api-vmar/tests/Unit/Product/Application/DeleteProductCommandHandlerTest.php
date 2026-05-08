<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Product\Application\Delete\DeleteProductCommand;
use SuperVMar\Product\Application\Delete\DeleteProductCommandHandler;
use SuperVMar\Product\Application\Delete\ProductDeleter;

final class DeleteProductCommandHandlerTest extends ApplicationTestCase
{
    private ProductDeleter $deleter;
    private DeleteProductCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(ProductDeleter::class);
        $this->handler = new DeleteProductCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_product_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeleteProductCommand(self::ID_PRODUCT));
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

        ($this->handler)(new DeleteProductCommand(self::ID_PRODUCT));

        $this->assertSame(self::ID_PRODUCT, $capturedId->value());
    }
}
