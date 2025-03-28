<?php

namespace SuperVMar\User\Application\Save\SaveUser;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveUserCommand implements Command
{
    public function __construct(
        private string $id,
        private string $username,
        private array $userData,
        private int $isAdmin,
        private ?string $password = null,
        private ?string $passwordRepeat = null,
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): ?string
    {
        return $this->password;
    }

    public function passwordRepeat(): ?string
    {
        return $this->passwordRepeat;
    }

    public function isAdmin(): ?int
    {
        return $this->isAdmin;
    }

    public function userData(): array
    {
        return $this->userData;
    }


}