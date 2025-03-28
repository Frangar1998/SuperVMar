<?php

namespace SuperVMar\Authentication\Domain;

use SuperVMar\Authentication\Domain\Exception\InvalidCredentialsException;
use SuperVMar\Authentication\Domain\ValueObject\Password;
use SuperVMar\Authentication\Domain\ValueObject\Username;

final readonly class AuthUser
{
    public function __construct(
        private Username $username,
        private Password $password
    )
    {
    }

    public function validateCredentials(Password $password): bool
    {
        if (!$this->password->equals($password)) {
            throw new InvalidCredentialsException();
        }
    }

    public function username(): Username
    {
        return $this->username;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Username($data['username']),
            new Password($data['password'])
        );
    }
}