<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Supplier\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Supplier\Application\Delete\DeleteSupplierCommand;
use SuperVMar\Supplier\Application\Delete\DeleteSupplierCommandHandler;
use SuperVMar\Supplier\Application\Delete\SupplierDeleter;

final class DeleteSupplierCommandHandlerTest extends TestCase
{
    private const string ID = '550e8400-e29b-41d4-a716-000000000040';

    private SupplierDeleter $deleter;
    private DeleteSupplierCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(SupplierDeleter::class);
        $this->handler = new DeleteSupplierCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_supplier_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeleteSupplierCommand(self::ID));
    }

    public function test_calls_deleter_with_correct_id(): void
    {
        $capturedId = null;
        $this->deleter
            ->expects($this->once())
            ->method('delete')
            ->willReturnCallback(function ($id) use (&$capturedId) {
                $capturedId = $id;
            });

        ($this->handler)(new DeleteSupplierCommand(self::ID));

        $this->assertSame(self::ID, $capturedId->value());
    }

    public function test_propagates_cannot_delete_exception_when_products_exist(): void
    {
        $this->deleter
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new CannotDeleteException('Cannot delete a supplier with existing supplied products.'));

        $this->expectException(CannotDeleteException::class);

        ($this->handler)(new DeleteSupplierCommand(self::ID));
    }
}
