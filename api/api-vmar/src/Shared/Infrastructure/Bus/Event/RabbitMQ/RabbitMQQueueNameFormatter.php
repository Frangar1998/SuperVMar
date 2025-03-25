<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event\RabbitMQ;

final readonly class RabbitMQQueueNameFormatter
{
    public static function retry(string $queueName): string
    {
        return "retry.{$queueName}";
    }

    public static function deadLetter(string $queueName): string
    {
        return "dead_letter.{$queueName}";
    }

    public static function clean(string $queueName): string
    {
        foreach (['retry.', 'dead_letter.'] as $clean) {
            $queueName = str_starts_with($queueName, $clean) ? substr($queueName, strlen($clean)) : $queueName;
        }
        return $queueName;
    }
}