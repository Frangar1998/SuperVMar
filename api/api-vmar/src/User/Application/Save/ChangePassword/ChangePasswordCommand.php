<?php

namespace SuperVMar\User\Application\Save\ChangePassword;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class ChangePasswordCommand implements Command
{
    public function __construct(
        private string $id,
        private string $currentPassword,
        private string $password,
        private string $passwordRepeat,
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function currentPassword(): string
    {
        return $this->currentPassword;
    }

    public function password(): ?string
    {
        return $this->password;
    }

    public function passwordRepeat(): ?string
    {
        return $this->passwordRepeat;
    }


}