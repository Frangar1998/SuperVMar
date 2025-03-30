<?php

namespace SuperVMar\User\Application\Save;

use SuperVMar\Shared\Domain\Bus\Event\EventBus;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\Entity\Allocations;
use SuperVMar\User\Domain\Entity\UserData;
use SuperVMar\User\Domain\Service\UserSearcher;
use SuperVMar\User\Domain\UserRepository;
use SuperVMar\User\Domain\ValueObject\IsAdmin;
use SuperVMar\User\Domain\ValueObject\Password;
use SuperVMar\User\Domain\ValueObject\Username;

final readonly class UserUpdater
{
    public function __construct(
        private UserSearcher $userSearcher,
        private UserRepository $userRepository,
        private EventBus $eventBus,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id          $id,
        Username    $username,
        UserData    $userData,
        IsAdmin     $isAdmin,
        Allocations $allocations
    ): void
    {
        $user = $this->userSearcher->search($id);

        $user->update($username, $userData, $isAdmin, $allocations);
        $this->userRepository->update($user);

        $this->eventBus->publish(...$user->pullDomainEvents());
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