<?php

namespace SuperVMar\User\Application\Save\SaveUser;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Application\Save\UserCreator;
use SuperVMar\User\Application\Save\UserUpdater;
use SuperVMar\User\Domain\Entity\Allocations;
use SuperVMar\User\Domain\Entity\UserData;
use SuperVMar\User\Domain\ValueObject\IsAdmin;
use SuperVMar\User\Domain\ValueObject\Password;
use SuperVMar\User\Domain\ValueObject\Username;

final readonly class SaveUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserCreator $userCreator,
        private UserUpdater $userUpdater
    )
    {
    }

    public function __invoke(SaveUserCommand $command): void
    {
        $id = new Id($command->id());
        $allocations = Allocations::fromArray($command->allocations());
        $username = new Username($command->username());
        $userData = UserData::fromPrimitives($command->userData());
        $isAdmin = new IsAdmin($command->isAdmin());
        $password = $command->password() !== null ? new Password($command->password(), $command->passwordRepeat()) : null;

        try {
            $this->userUpdater->update(
                $id,
                $username,
                $userData,
                $isAdmin,
                $allocations
            );
        } catch (ItemNotFoundException) {
            $this->userCreator->create(
                $id,
                $username,
                $userData,
                $isAdmin,
                $password,
                $allocations
            );
        }
    }
}