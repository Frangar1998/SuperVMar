<?php

namespace SuperVMar\User\Application\Delete;

use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\User\Domain\Exception\CannotDeleteAdminException;
use SuperVMar\User\Domain\Service\UserSearcher;
use SuperVMar\User\Domain\UserRepository;
use SuperVMar\User\Domain\ValueObject\Id;

final readonly class UserDeleter
{
    public function __construct(
        private UserSearcher $userSearcher,
        private UserRepository $userRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     * @throws CannotDeleteAdminException
     */
    public function delete(
        Id $id
    ): void
    {
        $user = $this->userSearcher->search($id);
        $user->checkIfIsAdmin();
        $this->userRepository->delete($user);
    }
}