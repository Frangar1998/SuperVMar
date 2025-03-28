<?php

namespace SuperVMar\App\Controller\User;

use JsonException;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\User\Application\Save\SaveUser\SaveUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new SaveUserCommand(
                $id,
                $data['username'],
                $data['userData'],
                $data['isAdmin'],
                $data['idSupermarket'],
                $data['idJob'],
                $data['password'] ?? null,
                $data['passwordRepeat'] ?? null,
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['username', 'userData', 'isAdmin', 'idSupermarket', 'idJob'];
    }
}