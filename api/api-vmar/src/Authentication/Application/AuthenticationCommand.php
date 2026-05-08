<?php

namespace SuperVMar\Authentication\Application;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class AuthenticationCommand implements Command
{
    public function __construct(
        private string $username,
        private string $password,
        private bool   $isLogin = false
    )
    {
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function isLogin(): bool
    {
        return $this->isLogin;
    }
}