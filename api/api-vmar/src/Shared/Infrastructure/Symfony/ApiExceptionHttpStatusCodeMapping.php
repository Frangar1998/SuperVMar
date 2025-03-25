<?php

namespace SuperVMar\Shared\Infrastructure\Symfony;

use InvalidArgumentException;
use SuperVMar\Shared\Domain\Exception\CommandNotRegisteredException;
use SuperVMar\Shared\Domain\Exception\DomainEventNotRegisteredException;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\InvalidUuidValueException;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Domain\Exception\QueryNotRegisteredException;
use SuperVMar\Supermarket\Domain\Exception\InvalidZoneCoordinatesException;
use Symfony\Component\HttpFoundation\Response;

final class ApiExceptionHttpStatusCodeMapping
{
    private const int DEFAULT_STATUS_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;
    private array $exceptions = [
        CommandNotRegisteredException::class => Response::HTTP_NOT_FOUND,
        DomainEventNotRegisteredException::class => Response::HTTP_NOT_FOUND,
        DuplicateItemException::class => Response::HTTP_CONFLICT,
        InternalErrorException::class => Response::HTTP_INTERNAL_SERVER_ERROR,
        InvalidUuidValueException::class => Response::HTTP_BAD_REQUEST,
        InvalidValueException::class => Response::HTTP_BAD_REQUEST,
        ItemNotFoundException::class => Response::HTTP_NOT_FOUND,
        MandatoryParamsException::class => Response::HTTP_BAD_REQUEST,
        QueryNotRegisteredException::class => Response::HTTP_NOT_FOUND,
        InvalidZoneCoordinatesException::class => Response::HTTP_BAD_REQUEST,
    ];

    public function statusCodeFor(string $exceptionClass): int
    {
        $statusCode = $this->exceptions[$exceptionClass] ?? self::DEFAULT_STATUS_CODE;

        if ($statusCode === null) {
            throw new InvalidArgumentException("There are no status code mapping for <$exceptionClass>");
        }

        return $statusCode;
    }
}