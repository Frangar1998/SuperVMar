<?php

namespace SuperVMar\Authentication\Application;

use SuperVMar\Authentication\Domain\ValueObject\Password;
use SuperVMar\Authentication\Domain\ValueObject\Username;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;

final readonly class AuthenticationCommandHandler implements CommandHandler
{
    public function __construct(
        private UserAuthenticator $authenticator
    )
    {
    }

    public function __invoke(AuthenticationCommand $command): void
    {
        $this->authenticator->authenticate(
            new Username($command->username()),
            new Password($command->password()),
            $command->isLogin()
        );
    }
}