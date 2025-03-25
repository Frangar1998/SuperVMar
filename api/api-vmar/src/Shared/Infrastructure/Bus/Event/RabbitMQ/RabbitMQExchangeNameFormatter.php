<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event\RabbitMQ;

final readonly class RabbitMQExchangeNameFormatter
{
    public static function retry(string $exchangeName): string
    {
        return "retry_{$exchangeName}";
    }

    public static function deadLetter(string $exchangeName): string
    {
        return "dead_letter_{$exchangeName}";
    }
}