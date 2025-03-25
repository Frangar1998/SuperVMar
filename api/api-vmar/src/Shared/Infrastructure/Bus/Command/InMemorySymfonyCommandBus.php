<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Command;

use ReflectionException;
use SuperVMar\Shared\Domain\Bus\Command\Command;
use SuperVMar\Shared\Domain\Bus\Command\CommandBus;
use SuperVMar\Shared\Domain\Exception\CommandNotRegisteredException;
use SuperVMar\Shared\Infrastructure\Bus\HandlerBuilder;
use SuperVMar\Shared\Infrastructure\Symfony\TransactionSymfonyMiddleware;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Throwable;

final class InMemorySymfonyCommandBus implements CommandBus
{
    private MessageBus $bus;

    /**
     * @throws ReflectionException
     */
    public function __construct(iterable $commandHandlers, TransactionSymfonyMiddleware $transactionMiddleware)
    {
        $this->bus = new MessageBus(
            [
                $transactionMiddleware,
                new HandleMessageMiddleware(
                    new HandlersLocator(HandlerBuilder::forCallables($commandHandlers))
                ),
            ]
        );
    }

    /** @throws Throwable */
    public function dispatch(Command $command): void
    {
        try {
            $this->bus->dispatch($command);
        } catch (NoHandlerForMessageException) {
            throw new CommandNotRegisteredException($command);
        } catch (HandlerFailedException $error) {
            throw $error->getPrevious() ?? $error;
        }
    }
}