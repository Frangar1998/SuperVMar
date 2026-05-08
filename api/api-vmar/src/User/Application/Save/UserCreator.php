<?php

namespace SuperVMar\User\Application\Save;

use SuperVMar\Shared\Domain\Bus\Event\EventBus;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\Entity\Allocations;
use SuperVMar\User\Domain\Entity\UserData;
use SuperVMar\User\Domain\User;
use SuperVMar\User\Domain\UserRepository;
use SuperVMar\User\Domain\ValueObject\IsAdmin;
use SuperVMar\User\Domain\ValueObject\Password;
use SuperVMar\User\Domain\ValueObject\Username;

readonly class UserCreator
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $eventBus,
    )
    {
    }

    public function create(
        Id          $id,
        Username    $username,
        UserData    $userData,
        IsAdmin     $isAdmin,
        Password    $password,
        Allocations $allocations
    ): void
    {
        $user = User::create(
            $id,
            $username,
            $userData,
            $isAdmin,
            $password,
            $allocations,
        );
        $this->userRepository->insert($user);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}