<?php

namespace SuperVMar\User\Application\Search\Users;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class UsersResponse implements Response
{
    public function __construct(
        private array $users,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'users' => $this->users,
        ];
    }
}