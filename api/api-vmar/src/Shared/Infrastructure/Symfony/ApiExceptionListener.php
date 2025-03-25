<?php

namespace SuperVMar\Shared\Infrastructure\Symfony;

use SuperVMar\Shared\Domain\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ApiExceptionListener
{
    public function __construct(private ApiExceptionHttpStatusCodeMapping $apiExceptionHttpStatusCodeMapping)
    {
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $event->setResponse(
            new JsonResponse(
                [
                    'code' => Utils::toSnakeCase(get_class($exception)),
                    'message' => $exception->getMessage(),
                ],
                $this->apiExceptionHttpStatusCodeMapping->statusCodeFor(get_class($exception))
            )
        );
    }
}