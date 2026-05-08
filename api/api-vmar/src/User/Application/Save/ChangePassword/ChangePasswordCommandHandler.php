<?php

namespace SuperVMar\User\Application\Save\ChangePassword;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Application\Save\UserUpdater;
use SuperVMar\User\Domain\ValueObject\Password;

final readonly class ChangePasswordCommandHandler implements CommandHandler
{
    public function __construct(
        private UserUpdater $userUpdater,
    )
    {
    }

    public function __invoke(ChangePasswordCommand $command): void
    {
        $id = new Id($command->id());
        $currentPassword = new Password($command->currentPassword(), $command->currentPassword());
        $newPassword = new Password($command->password(), $command->passwordRepeat());

        $this->userUpdater->updatePassword(
            $id,
            $currentPassword,
            $newPassword
        );
    }
}