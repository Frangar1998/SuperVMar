<?php

namespace SuperVMar\User\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Users extends Collection
{
    protected function type(): string
    {
        return User::class;
    }

    public static function fromArray(array $users): self
    {
        return new self(
            array_map(
                fn(array $user) => User::fromArray($user),
                $users
            )
        );
    }
}