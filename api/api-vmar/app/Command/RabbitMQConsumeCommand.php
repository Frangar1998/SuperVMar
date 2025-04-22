<?php

namespace SuperVMar\App\Command;

use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;
use SuperVMar\Shared\Infrastructure\Bus\Event\RabbitMQ\RabbitMQDomainEventsConsumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Traversable;

class RabbitMQConsumeCommand extends Command
{
    protected static string $defaultName = 'rabbitmq:consume';

    /** @var DomainEventSubscriber[] */
    private array $consumerList;

    /**
     * @param iterable<DomainEventSubscriber> $consumerList
     */
    public function __construct(
        private readonly RabbitMQDomainEventsConsumer $consumer,
        iterable $consumerList,
    ) {
        parent::__construct(self::$defaultName);
        $this->consumerList = $consumerList instanceof Traversable ?
            iterator_to_array($consumerList) :
            (array) $consumerList;
    }

    protected function configure(): void
    {
        $this->setDescription('Consume a message from RabbitMQ')
            ->addArgument('consumer', InputArgument::REQUIRED, 'Consumer class name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consumerClassName = (string)$input->getArgument('consumer');
        $consumer = $this->getConsumerFromConsumerClassName($consumerClassName);

        if ($consumer === null) {
            $output->writeln("Consumer <{$consumerClassName}> not found");
            $output->writeln('Consumers available:');
            array_map(static function (string $consumerName) use ($output) {
                $output->writeln(" - {$consumerName}");
            }, $this->getAvailableConsumersName());

            return self::FAILURE;
        }

        if (is_callable($consumer)) {
            $this->consumer->consume($consumer);
        }

        return self::SUCCESS;
    }

    /** @return array<int, string> */
    private function getAvailableConsumersName(): array
    {
        return array_map(function (DomainEventSubscriber $consumer) {
            $className = $consumer::class;
            $namespace = explode('\\', $className);

            return end($namespace);
        }, $this->consumerList);
    }

    private function getConsumerFromConsumerClassName(string $consumerClassName): ?DomainEventSubscriber
    {
        return array_find(
            $this->consumerList,
            fn($value) => strpos($value::class, $consumerClassName)
        );

    }
}