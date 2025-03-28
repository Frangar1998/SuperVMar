<?php

namespace SuperVMar\User\Application\Save;

use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\User\Domain\Entity\UserData;
use SuperVMar\User\Domain\Service\UserSearcher;
use SuperVMar\User\Domain\UserRepository;
use SuperVMar\User\Domain\ValueObject\Id;
use SuperVMar\User\Domain\ValueObject\IsAdmin;
use SuperVMar\User\Domain\ValueObject\Password;
use SuperVMar\User\Domain\ValueObject\Username;

final readonly class UserUpdater
{
    public function __construct(
        private UserSearcher $userSearcher,
        private UserRepository $userRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id       $id,
        Username $username,
        UserData $userData,
        IsAdmin  $isAdmin
    ): void
    {
        $user = $this->userSearcher->search($id);
        $user->changeUsername($username);
        $user->changeUserData($userData);
        $user->changeIsAdmin($isAdmin);
        $this->userRepository->update($user);
    }

    public function updatePassword(
        Id $id,
        Password $currentPassword,
        Password $newPassword
    ): void
    {
        $user = $this->userSearcher->search($id);
        $user->changePassword($newPassword, $currentPassword);
        $this->userRepository->update($user);
    }
}