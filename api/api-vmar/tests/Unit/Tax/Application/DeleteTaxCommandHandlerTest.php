<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Tax\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Tax\Application\Delete\DeleteTaxCommand;
use SuperVMar\Tax\Application\Delete\DeleteTaxCommandHandler;
use SuperVMar\Tax\Application\Delete\TaxDeleter;

final class DeleteTaxCommandHandlerTest extends TestCase
{
    private const string ID = '550e8400-e29b-41d4-a716-000000000020';

    private TaxDeleter $deleter;
    private DeleteTaxCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(TaxDeleter::class);
        $this->handler = new DeleteTaxCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_tax_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeleteTaxCommand(self::ID));
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

        ($this->handler)(new DeleteTaxCommand(self::ID));

        $this->assertSame(self::ID, $capturedId->value());
    }

    public function test_propagates_cannot_delete_exception_when_products_exist(): void
    {
        $this->deleter
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new CannotDeleteException('Cannot delete a tax with existing taxed products.'));

        $this->expectException(CannotDeleteException::class);

        ($this->handler)(new DeleteTaxCommand(self::ID));
    }
}
