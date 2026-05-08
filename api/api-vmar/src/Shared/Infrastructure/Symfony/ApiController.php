<?php

namespace SuperVMar\Shared\Infrastructure\Symfony;

use JsonException;
use SuperVMar\Shared\Domain\Bus\Command\Command;
use SuperVMar\Shared\Domain\Bus\Command\CommandBus;
use SuperVMar\Shared\Domain\Bus\Query\Query;
use SuperVMar\Shared\Domain\Bus\Query\QueryBus;
use SuperVMar\Shared\Domain\Bus\Query\Response;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Domain\Utils;
use Symfony\Component\HttpFoundation\Request;

abstract readonly class ApiController
{
    public function __construct(
        private QueryBus                  $queryBus,
        private CommandBus                $commandBus,
        ApiExceptionHttpStatusCodeMapping $exceptionHandler
    )
    {
    }

    protected function ask(Query $query): ?Response
    {
        return $this->queryBus->ask($query);
    }

    protected function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }

    abstract protected function mandatoryParams(): array;

    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    protected function dataFromRequest(Request $request, bool $isMultipart = false): array
    {
        if ($isMultipart) {
            $data = [];
            if ($request->request->has('data')) {
                $data['data'] = Utils::jsonDecode($request->request->get('data'));
            }
        } else {
            $data = Utils::jsonDecode($request->getContent());
        }

        $missingParams = array_filter(
            $this->mandatoryParams(),
            static fn ($param) => !isset($data[$param])
        );
        if ($missingParams) {
            throw new MandatoryParamsException(implode(',', $missingParams));
        }

        return $data;
    }
}