<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

final class RabbitMQConnection
{
    private const int TTL = 3000;
    private const int DEFAULT_PREFETCH_COUNT = 1;
    private const int DEFAULT_MAX_MESSAGES = 1;
    protected AMQPChannel $channel;

    public function __construct(
        protected string $exchangeName,
        private readonly AbstractConnection $AMQPStreamConnection,
        private readonly int $maxMessages = self::DEFAULT_MAX_MESSAGES,
        private readonly int $prefetchCount = self::DEFAULT_PREFETCH_COUNT,
    )
    {
        $connection = $this->AMQPStreamConnection;
        if (!$connection->isConnected()) {
            $connection->reconnect();
        }
        $this->channel = $connection->channel();

        $exchangeNames = [
            RabbitMQExchangeNameFormatter::retry($exchangeName),
            RabbitMQExchangeNameFormatter::deadLetter($exchangeName),
            $exchangeName,
        ];
        foreach ($exchangeNames as $name) {
            $this->declareExchange($name);
        }
    }

    public function consume(string $queue, ?callable $callback = null): void
    {
        $this->channel->basic_consume(
            queue: $queue,
            callback: $callback,
        );

        $count = 0;
        while ($this->channel->is_open() && ($this->maxMessages == 0 || $count < $this->maxMessages)) {
            $this->channel->wait();
            ++$count;
        }

        $this->channel->close();
        $this->AMQPStreamConnection->close();
    }

    public function publish(string $body, string $routingKey, int $redeliveryCount = 0): void
    {
        $headers = new AMQPTable([
            'redelivery_count' => $redeliveryCount,
        ]);

        $AMQPMessage = new AMQPMessage(
            $body,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $AMQPMessage->set('application_headers', $headers);
        $this->channel->basic_publish(
            $AMQPMessage,
            $this->exchangeName,
            $routingKey
        );
    }

    public function getExchangeName(): string
    {
        return $this->exchangeName;
    }

    public function setExchangeName(string $exchangeName): RabbitMQConnection
    {
        $this->exchangeName = $exchangeName;

        return $this;
    }

    public function declareQueue(string $queueName, string $exchangeName, string $routingKey, mixed $arguments = []): void
    {
        [$queueName, ,] = $this->channel->queue_declare(
            queue: $queueName,
            durable: true,
            auto_delete: false,
            arguments: $arguments
        );

        if (!str_starts_with($exchangeName, 'retry') && !str_starts_with($exchangeName, 'dead')) {
            $this->channel->queue_bind($queueName, $exchangeName, $routingKey);
        }
        $this->channel->queue_bind($queueName, $exchangeName, $queueName);
        if ($this->prefetchCount > 0) {
            $this->channel->basic_qos(0, $this->prefetchCount, null);
        }
    }

    public function declareQueues(string $queueName, string $routingKey): void
    {
        $queueName = RabbitMQQueueNameFormatter::clean($queueName);
        $queues = [
            $this->getExchangeName() => $queueName,
            RabbitMQExchangeNameFormatter::retry($this->getExchangeName()) => RabbitMQQueueNameFormatter::retry($queueName),
            RabbitMQExchangeNameFormatter::deadLetter($this->getExchangeName()) => RabbitMQQueueNameFormatter::deadLetter($queueName),
        ];
        foreach ($queues as $exchange => $queue) {
            $arguments = str_starts_with($exchange, 'retry') ?
                new AMQPTable([
                    'x-message-ttl' => self::TTL,
                    'x-dead-letter-exchange' => $this->getExchangeName(),
                    'x-dead-letter-routing-key' => $queueName,
                ])
                : [];

            $this->declareQueue($queue, $exchange, $routingKey, $arguments);
        }
    }

    private function declareExchange(string $exchangeName): void
    {
        $this->channel->exchange_declare(
            $exchangeName,
            type: AMQPExchangeType::TOPIC,
            durable: true,
            auto_delete: false,
        );
    }
}