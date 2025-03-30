<?php

namespace SuperVMar\Shared\Infrastructure\Bus;

use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionType;
use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;

final class HandlerBuilder
{
    /**
     * @throws ReflectionException
     */
    public static function forCallables(iterable $callables) : array
    {
        $callableHandlers = [];
        foreach ($callables as $callable) {
            $envelop = self::extractFirstParam($callable);

            if (!array_key_exists($envelop, $callableHandlers)) {
                $callableHandlers[self::extractFirstParam($callable)] = [];
            }

            $callableHandlers[self::extractFirstParam($callable)][] = $callable;
        }
        return $callableHandlers;
    }

    /**
     * @throws ReflectionException
     */
    private static function extractFirstParam(object|string $class) : string|null
    {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod('__invoke');
        if ($method->getNumberOfParameters() === 1) {
            $firstParameterType = $method->getParameters()[0]->getType();

            if (!$firstParameterType instanceof ReflectionType) {
                throw new LogicException('Missing type hint for the first parameter of __invoke');
            }

            return $firstParameterType->getName();
        }
        return null;
    }

    public static function forPipedCallables(iterable $callables): array
    {
        return self::reduce($callables);
    }

    private static function reduce(iterable $callables): array
    {
        $eventSubscribers = [];
        foreach ($callables as $value) {
            $eventSubscribers = self::pipedCallablesReducer($eventSubscribers, $value);
        }
        return $eventSubscribers;
    }

    private static function pipedCallablesReducer(array $eventSubscribers, DomainEventSubscriber $subscriber): array
    {
        $subscribedEvents = $subscriber::subscribedTo();

        foreach ($subscribedEvents as $subscribedEvent) {
            $eventSubscribers[$subscribedEvent][] = $subscriber;
        }

        return $eventSubscribers;
    }
}