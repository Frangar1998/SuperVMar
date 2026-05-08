<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\User\Application\Delete\DeleteUserCommand;
use SuperVMar\User\Application\Delete\DeleteUserCommandHandler;
use SuperVMar\User\Application\Delete\UserDeleter;

final class DeleteUserCommandHandlerTest extends ApplicationTestCase
{
    private UserDeleter $deleter;
    private DeleteUserCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(UserDeleter::class);
        $this->handler = new DeleteUserCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_user_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeleteUserCommand(self::ID_USER));
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

        ($this->handler)(new DeleteUserCommand(self::ID_USER));

        $this->assertSame(self::ID_USER, $capturedId->value());
    }
}
