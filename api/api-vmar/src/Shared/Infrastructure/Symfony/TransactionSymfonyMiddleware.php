<?php

namespace SuperVMar\Shared\Infrastructure\Symfony;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

final readonly class TransactionSymfonyMiddleware implements MiddlewareInterface
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $this->connection->beginTransaction();
        try {
            $handle = $stack->next()->handle($envelope, $stack);
            $this->connection->commit();
        } catch (Throwable $error) {
            $this->connection->rollback();
            throw $error;
        }
        return $handle;
    }
}