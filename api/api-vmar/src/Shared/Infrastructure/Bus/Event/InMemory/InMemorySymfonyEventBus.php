<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event\InMemory;

use ReflectionException;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\EventBus;
use SuperVMar\Shared\Infrastructure\Bus\HandlerBuilder;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final readonly class InMemorySymfonyEventBus implements EventBus
{
    private MessageBus $bus;

    /**
     * @throws ReflectionException
     */
    public function __construct(iterable $subscribers)
    {
        $this->bus = new MessageBus(
            [
                new HandleMessageMiddleware(
                    new HandlersLocator(HandlerBuilder::forPipedCallables($subscribers))
                ),
            ]
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->bus->dispatch($event);
            } catch (NoHandlerForMessageException) {
            }
        }
    }
}