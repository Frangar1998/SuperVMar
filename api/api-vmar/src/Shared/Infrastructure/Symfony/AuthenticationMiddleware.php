<?php

namespace SuperVMar\Shared\Infrastructure\Symfony;

use SuperVMar\Authentication\Application\AuthenticationCommand;
use SuperVMar\Authentication\Domain\Exception\InvalidCredentialsException;
use SuperVMar\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthenticationMiddleware
{
    public function __construct(
        private CommandBus $bus
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $shouldAuthenticate = $event->getRequest()->attributes->get('auth', true);

        if ($shouldAuthenticate) {
            $user = $event->getRequest()->headers->get('php-auth-user');
            $pass = $event->getRequest()->headers->get('php-auth-password');

            $this->hasIntroducedCredentials($user)
                ? $this->authenticate($user, $pass, $event)
                : $this->askForCredentials($event);
        }
    }

    private function hasIntroducedCredentials(?string $user): bool
    {
        return null !== $user;
    }

    private function authenticate(string $user, string $pass, RequestEvent $event): void
    {
        try {
            $this->bus->dispatch(new AuthenticationCommand($user, $pass));

            $this->addUserDataToRequest($user, $event);
        } catch (InvalidCredentialsException) {
            $event->setResponse(new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED));
        }
    }

    private function addUserDataToRequest(string $user, RequestEvent $event): void
    {
        $event->getRequest()->attributes->set('authenticated_username', $user);
    }

    private function askForCredentials(RequestEvent $event): void
    {
        $event->setResponse(
            new Response('', Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Basic realm="SuperVMar"'])
        );
    }
}