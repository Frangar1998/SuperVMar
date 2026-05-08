<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\User\Application\Save\ChangePassword\ChangePasswordCommand;
use SuperVMar\User\Application\Save\ChangePassword\ChangePasswordCommandHandler;
use SuperVMar\User\Application\Save\UserUpdater;

final class ChangePasswordCommandHandlerTest extends ApplicationTestCase
{
    private UserUpdater $updater;
    private ChangePasswordCommandHandler $handler;

    protected function setUp(): void
    {
        $this->updater = $this->createMock(UserUpdater::class);
        $this->handler = new ChangePasswordCommandHandler($this->updater);
    }

    public function test_delegates_to_updater_update_password(): void
    {
        $this->updater->expects($this->once())->method('updatePassword');

        ($this->handler)(new ChangePasswordCommand(
            id: self::ID_USER,
            currentPassword: self::VALID_PASSWORD,
            password: self::NEW_VALID_PASSWORD,
            passwordRepeat: self::NEW_VALID_PASSWORD,
        ));
    }

    public function test_calls_update_password_with_correct_user_id(): void
    {
        $capturedId = null;

        $this->updater
            ->expects($this->once())
            ->method('updatePassword')
            ->willReturnCallback(function ($id) use (&$capturedId) {
                $capturedId = $id;
            });

        ($this->handler)(new ChangePasswordCommand(
            id: self::ID_USER,
            currentPassword: self::VALID_PASSWORD,
            password: self::NEW_VALID_PASSWORD,
            passwordRepeat: self::NEW_VALID_PASSWORD,
        ));

        $this->assertSame(self::ID_USER, $capturedId->value());
    }
}
