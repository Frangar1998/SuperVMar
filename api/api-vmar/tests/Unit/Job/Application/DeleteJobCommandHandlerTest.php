<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Job\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Job\Application\Delete\DeleteJobCommand;
use SuperVMar\Job\Application\Delete\DeleteJobCommandHandler;
use SuperVMar\Job\Application\Delete\JobDeleter;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;

final class DeleteJobCommandHandlerTest extends TestCase
{
    private const string ID = '550e8400-e29b-41d4-a716-000000000041';

    private JobDeleter $deleter;
    private DeleteJobCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(JobDeleter::class);
        $this->handler = new DeleteJobCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_job_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeleteJobCommand(self::ID));
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

        ($this->handler)(new DeleteJobCommand(self::ID));

        $this->assertSame(self::ID, $capturedId->value());
    }

    public function test_propagates_cannot_delete_exception_when_allocations_exist(): void
    {
        $this->deleter
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new CannotDeleteException('Cannot delete a job with existing allocations.'));

        $this->expectException(CannotDeleteException::class);

        ($this->handler)(new DeleteJobCommand(self::ID));
    }
}
