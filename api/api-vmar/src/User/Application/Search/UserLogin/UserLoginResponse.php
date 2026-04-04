<?php

namespace SuperVMar\User\Application\Search\UserLogin;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class UserLoginResponse implements Response
{
    public function __construct(
        private string  $id,
        private string  $username,
        private string  $password,
        private int     $isAdmin,
        private ?string $job = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'isAdmin' => $this->isAdmin,
            'job' => $this->job,
        ];
    }
}