<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\User\Application\Save\SaveUser\SaveUserCommand;
use SuperVMar\User\Application\Save\SaveUser\SaveUserCommandHandler;
use SuperVMar\User\Application\Save\UserCreator;
use SuperVMar\User\Application\Save\UserUpdater;

final class SaveUserCommandHandlerTest extends ApplicationTestCase
{
    private UserCreator $creator;
    private UserUpdater $updater;
    private SaveUserCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createMock(UserCreator::class);
        $this->updater = $this->createMock(UserUpdater::class);
        $this->handler = new SaveUserCommandHandler($this->creator, $this->updater);
    }

    private function createCommand(): SaveUserCommand
    {
        return new SaveUserCommand(
            id: self::ID_USER,
            username: 'test_user',
            userData: $this->userDataArray(),
            isAdmin: 0,
            allocations: [],
            password: self::VALID_PASSWORD,
            passwordRepeat: self::VALID_PASSWORD,
        );
    }

    private function updateCommand(): SaveUserCommand
    {
        return new SaveUserCommand(
            id: self::ID_USER,
            username: 'test_user',
            userData: $this->userDataArray(),
            isAdmin: 0,
            allocations: [],
            password: null,
            passwordRepeat: null,
        );
    }

    public function test_updates_existing_user_when_found(): void
    {
        $this->updater->expects($this->once())->method('update');
        $this->creator->expects($this->never())->method('create');

        ($this->handler)($this->updateCommand());
    }

    public function test_creates_user_when_updater_reports_not_found(): void
    {
        $this->updater
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new ItemNotFoundException('User', ['id' => self::ID_USER]));

        $this->creator->expects($this->once())->method('create');

        ($this->handler)($this->createCommand());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $this->updater->expects($this->once())->method('update');
        $this->creator->expects($this->never())->method('create');

        ($this->handler)($this->updateCommand());
    }

    public function test_creates_admin_user_with_is_admin_flag(): void
    {
        $adminCommand = new SaveUserCommand(
            id: self::ID_USER,
            username: 'admin_user',
            userData: $this->userDataArray(),
            isAdmin: 1,
            allocations: [],
            password: self::VALID_PASSWORD,
            passwordRepeat: self::VALID_PASSWORD,
        );

        $this->updater
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new ItemNotFoundException('User', ['id' => self::ID_USER]));

        $this->creator->expects($this->once())->method('create');

        ($this->handler)($adminCommand);
    }
}
