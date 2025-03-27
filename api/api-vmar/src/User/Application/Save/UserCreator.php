<?php

namespace SuperVMar\User\Application\Save;

use SuperVMar\User\Domain\Entity\UserData;
use SuperVMar\User\Domain\ValueObject\Id;
use SuperVMar\User\Domain\ValueObject\IsAdmin;
use SuperVMar\User\Domain\ValueObject\Password;
use SuperVMar\User\Domain\ValueObject\Username;
use SuperVMar\User\Domain\User;
use SuperVMar\User\Domain\UserRepository;

final readonly class UserCreator
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function create(
        Id       $id,
        Username $username,
        UserData $userData,
        IsAdmin  $isAdmin,
        Password $password,
    ): void
    {
        $this->userRepository->insert(
            User::create(
                $id,
                $username,
                $userData,
                $isAdmin,
                $password,
            )
        );

    }
}