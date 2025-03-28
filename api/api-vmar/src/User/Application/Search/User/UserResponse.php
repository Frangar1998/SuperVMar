<?php

namespace SuperVMar\User\Application\Search\User;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class UserResponse implements Response
{
    public function __construct(
        private string $id,
        private string $username,
        private array $userData,
        private array $allocations
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'userData' => $this->userData,
            'allocations' => $this->allocations
        ];
    }
}