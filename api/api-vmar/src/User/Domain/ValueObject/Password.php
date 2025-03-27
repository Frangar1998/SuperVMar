<?php

namespace SuperVMar\User\Domain\ValueObject;

use SuperVMar\User\Domain\Exception\InvalidPasswordException;
use SuperVMar\User\Domain\Service\PasswordRequirements;

final readonly class Password
{
    protected string $encodedPassword;

    public function __construct(
        private string $password,
        private string $passwordRepeat,
    )
    {
        $this->validate($password, $passwordRepeat);
        $this->encodedPassword = $this->encode($password);
    }

    public function valueEncoded(): string
    {
        return $this->encodedPassword;
    }

    public function value(): string
    {
        return $this->password;
    }

    protected function encode(string $password): string
    {
        if ($this->isEncoded($password)) {
            return $password;
        }

        return password_hash(md5($password), PASSWORD_DEFAULT);
    }

    protected function isEncoded(string $password): bool
    {
        return password_get_info($password)['algo'] !== null;
    }

    public function equals(self $other): bool
    {
        return password_verify(md5($other->value()), $this->encodedPassword);
    }

    protected function validate(string $password, string $passwordRepeat): void
    {
        $errors = PasswordRequirements::validatePassword($password, $passwordRepeat);
        if (count($errors) > 0) {
            throw new InvalidPasswordException($errors);
        }
    }

    public function __toString(): string
    {
        return $this->encodedPassword;
    }
}