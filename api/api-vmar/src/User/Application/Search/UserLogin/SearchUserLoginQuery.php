<?php

namespace SuperVMar\User\Application\Search\UserLogin;

use SuperVMar\Shared\Domain\Bus\Query\Query;

final readonly class SearchUserLoginQuery implements Query
{
    public function __construct(
        private string $username
    )
    {
    }

    public function username(): string
    {
        return $this->username;
    }
}