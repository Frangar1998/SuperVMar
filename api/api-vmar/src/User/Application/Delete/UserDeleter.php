<?php

namespace SuperVMar\User\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Event\EventBus;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\Service\UserSearcher;
use SuperVMar\User\Domain\UserRepository;

readonly class UserDeleter
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
     * @throws CannotDeleteException
     */
    public function delete(
        Id $id
    ): void
    {
        $user = $this->userSearcher->search($id);
        $user->checkIfIsAdminToDelete();

        $this->eventBus->publish(...$user->pullDomainEvents());

        $this->userRepository->delete($user);
    }
}