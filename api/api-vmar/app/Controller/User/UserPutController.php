<?php

namespace SuperVMar\App\Controller\User;

use JsonException;
use SuperVMar\Shared\Domain\Bus\Command\CommandBus;
use SuperVMar\Shared\Domain\Bus\Query\QueryBus;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Shared\Infrastructure\Symfony\ApiExceptionHttpStatusCodeMapping;
use SuperVMar\User\Application\Save\SaveUser\SaveUserCommand;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserPutController extends ApiController
{
    public function __construct(
        QueryBus $queryBus,
        CommandBus $commandBus,
        ApiExceptionHttpStatusCodeMapping $exceptionHandler,
        private Security $security,
    ) {
        parent::__construct($queryBus, $commandBus, $exceptionHandler);
    }

    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request);

        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            $data['isAdmin'] = 0;
        }
        $this->dispatch(
            new SaveUserCommand(
                $id,
                $data['username'],
                $data['userData'],
                $data['isAdmin'],
                $data['allocations'],
                $data['password'] ?? null,
                $data['passwordRepeat'] ?? null,
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['username', 'userData', 'isAdmin', 'allocations'];
    }
}