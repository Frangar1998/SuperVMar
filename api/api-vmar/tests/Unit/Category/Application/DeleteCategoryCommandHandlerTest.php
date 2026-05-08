<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Category\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Category\Application\Delete\CategoryDeleter;
use SuperVMar\Category\Application\Delete\DeleteCategoryCommand;
use SuperVMar\Category\Application\Delete\DeleteCategoryCommandHandler;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;

final class DeleteCategoryCommandHandlerTest extends TestCase
{
    private const string ID = '550e8400-e29b-41d4-a716-000000000030';

    private CategoryDeleter $deleter;
    private DeleteCategoryCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(CategoryDeleter::class);
        $this->handler = new DeleteCategoryCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_category_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeleteCategoryCommand(self::ID));
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

        ($this->handler)(new DeleteCategoryCommand(self::ID));

        $this->assertSame(self::ID, $capturedId->value());
    }

    public function test_propagates_cannot_delete_exception(): void
    {
        $this->deleter
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new CannotDeleteException('Cannot delete.'));

        $this->expectException(CannotDeleteException::class);

        ($this->handler)(new DeleteCategoryCommand(self::ID));
    }
}
