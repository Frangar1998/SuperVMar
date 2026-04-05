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
        private array $roles = [],
    ) {
        $this->roles = $this->computeRoles();
    }

    private function computeRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->isAdmin === 1) {
            $roles[] = 'ROLE_ADMIN';
            return array_unique($roles);
        }

        $job = strtolower($this->job ?? '');
        if (str_contains($job, 'encargado')) {
            $roles[] = 'ROLE_ENCARGADO';
        }
        if (str_contains($job, 'cajero')) {
            $roles[] = 'ROLE_CAJERO';
        }
        if (str_contains($job, 'reponedor')) {
            $roles[] = 'ROLE_REPONEDOR';
        }

        return array_unique($roles);
    }

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
