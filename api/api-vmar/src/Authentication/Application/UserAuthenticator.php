<?php

namespace SuperVMar\Authentication\Application;

use SuperVMar\Authentication\Domain\AuthUser;
use SuperVMar\Authentication\Domain\Exception\InvalidCredentialsException;
use SuperVMar\Authentication\Domain\Service\UserSearcher;
use SuperVMar\Authentication\Domain\ValueObject\Password;
use SuperVMar\Authentication\Domain\ValueObject\Username;

final readonly class UserAuthenticator
{
    public function __construct(
        private UserSearcher $userSearcher
    )
    {
    }

    public function authenticate(Username $username, Password $password): void
    {
        $user = $this->userSearcher->search($username);

        $user->validateCredentials($password);
    }
}