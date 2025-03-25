<?php

namespace SuperVMar\Shared\Infrastructure\Bus\Event\RabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;
use SuperVMar\Shared\Infrastructure\Bus\Event\DomainEventJsonDeserializer;

final class RabbitMQDomainEventsConsumer
{
    private const DEFAULT_MAX_RETRIES = 3;

    public function __construct(
        private readonly RabbitMqConnection          $rabbitMqConnection,
        private readonly DomainEventJsonDeserializer $domainEventJsonDeserializer,
        private readonly int                         $maxRetries = self::DEFAULT_MAX_RETRIES
    ) {
    }

    public function consume(DomainEventSubscriber $subscriber): void
    {
        $eventsSubscribedTo = $subscriber->subscribedTo();
        /** @var DomainEvent $event */
        foreach ($eventsSubscribedTo as $event) {
            $this->rabbitMqConnection->declareQueues($subscriber::queue(), $event::eventName());
        }

        $this->rabbitMqConnection->consume($subscriber::queue(), $this->consumer($subscriber));
    }

    private function consumer(DomainEventSubscriber $subscriber): callable
    {
        return function (AMQPMessage $envelope) use ($subscriber) {
            try {
                $event = $this->domainEventJsonDeserializer->deserialize($envelope->getBody());
                $subscriber($event);
            } catch (\Throwable $error) {
                $this->addErrorMessageToBody($envelope, $error->getMessage());
                $this->handleConsumptionError($envelope, $subscriber::queue());
                throw $error;
            }
            $envelope->ack();
        };
    }

    private function addErrorMessageToBody(AMQPMessage $envelope, string $message): void
    {
        $body = json_decode($envelope->getBody(), true);
        $body['error'][] = $message;
        $envelope->setBody(json_encode($body));
    }

    private function handleConsumptionError(AMQPMessage $envelope, string $queue): void
    {
        $this->maxRetriesReached($envelope)
            ? $this->sendToDeadLetter($envelope, $queue)
            : $this->sendToRetry($envelope, $queue);
        $envelope->ack();
    }

    private function maxRetriesReached(AMQPMessage $envelope): bool
    {
        return $this->countRedelivery($envelope) >= $this->maxRetries;
    }

    private function sendToDeadLetter(AMQPMessage $envelope, string $queue): void
    {
        $exchangeName = RabbitMqExchangeNameFormatter::deadLetter($this->rabbitMqConnection->getExchangeName());
        $queue = RabbitMqQueueNameFormatter::deadLetter($queue);
        $this->sendMessageTo($exchangeName, $envelope, $queue);
    }

    private function sendToRetry(AMQPMessage $envelope, string $queue): void
    {
        $exchangeName = RabbitMqExchangeNameFormatter::retry($this->rabbitMqConnection->getExchangeName());
        $queue = RabbitMqQueueNameFormatter::retry($queue);
        $this->sendMessageTo($exchangeName, $envelope, $queue);
    }

    private function sendMessageTo(string $exchangeName, AMQPMessage $envelope, string $queue): void
    {
        $redeliveryCount = $this->countRedelivery($envelope);
        $this->rabbitMqConnection
            ->setExchangeName($exchangeName)
            ->publish(
                $envelope->getBody(),
                $queue,
                $redeliveryCount
            );
    }

    private function countRedelivery(AMQPMessage $envelope): int
    {
        $headers = $envelope->get('application_headers');
        $data = $headers->getNativeData();
        return \array_key_exists('redelivery_count', $data) ? $data['redelivery_count'] + 1 : 1;
    }
}