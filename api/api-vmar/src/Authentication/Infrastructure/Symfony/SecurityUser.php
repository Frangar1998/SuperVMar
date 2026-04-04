<?php

namespace SuperVMar\Authentication\Infrastructure\Symfony;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private readonly string $id,
        private readonly string $username,
        private readonly string $hashedPassword,
        private readonly int $isAdmin,
        private readonly ?string $job,
        private readonly array $roles = ['ROLE_USER'],
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->hashedPassword;
    }

    public function isAdmin(): int
    {
        return $this->isAdmin;
    }

    public function job(): ?string
    {
        return $this->job;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
    
    public function eraseCredentials(): void {}
}
