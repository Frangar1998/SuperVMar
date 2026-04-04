<?php

namespace SuperVMar\Authentication\Infrastructure\Symfony;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class Md5PasswordHasher implements PasswordHasherInterface
{
    public function hash(#[\SensitiveParameter] string $plainPassword): string
    {
        return password_hash(md5($plainPassword), PASSWORD_DEFAULT);
    }

    public function verify(string $hashedPassword, #[\SensitiveParameter] string $plainPassword): bool
    {
        return password_verify(md5($plainPassword), $hashedPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return password_needs_rehash($hashedPassword, PASSWORD_DEFAULT);
    }
}
