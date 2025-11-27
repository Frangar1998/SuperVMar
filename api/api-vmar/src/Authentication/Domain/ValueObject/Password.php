<?php

namespace SuperVMar\Authentication\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;

final readonly class Password
{
    protected string $encodedPassword;

    public function __construct(
        private string $password,
    )
    {
        $this->validate($password);
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
        return $this->encodedPassword === $other->encodedPassword;
    }

    public function equalsLogin(self $other): bool
    {
        return password_verify(md5($other->value()), $this->encodedPassword);
    }

    protected function validate(string $password): void
    {
        if (empty($password)) {
            throw new InvalidValueException('Password cannot be empty.');
        }
    }

    public function __toString(): string
    {
        return $this->encodedPassword;
    }
}