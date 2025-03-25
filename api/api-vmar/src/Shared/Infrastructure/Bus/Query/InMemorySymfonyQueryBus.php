<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Query;

use ReflectionException;
use SuperVMar\Shared\Domain\Bus\Query\Query;
use SuperVMar\Shared\Domain\Bus\Query\QueryBus;
use SuperVMar\Shared\Domain\Bus\Query\Response;
use SuperVMar\Shared\Domain\Exception\QueryNotRegisteredException;
use SuperVMar\Shared\Infrastructure\Bus\HandlerBuilder;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class InMemorySymfonyQueryBus implements QueryBus
{
    private MessageBus $bus;

    /**
     * @throws ReflectionException
     */
    public function __construct(iterable $queryHandlers)
    {
        $this->bus = new MessageBus(
            [
                new HandleMessageMiddleware(
                    new HandlersLocator(HandlerBuilder::forCallables($queryHandlers))
                ),
            ]
        );
    }

    public function ask(Query $query): ?Response
    {
        try {
            /** @var HandledStamp $stamp */
            $stamp = $this->bus->dispatch($query)->last(HandledStamp::class);
            return $stamp->getResult();
        } catch (NoHandlerForMessageException) {
            throw new QueryNotRegisteredException($query);
        }
    }
}