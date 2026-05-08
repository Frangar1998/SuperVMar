<?php

namespace SuperVMar\App\Controller\User;

use JsonException;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\User\Application\Save\ChangePassword\ChangePasswordCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserPasswordPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new ChangePasswordCommand(
                $id,
                $data['currentPassword'],
                $data['password'],
                $data['passwordRepeat'],
            )
        );

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return ['currentPassword', 'password', 'passwordRepeat'];
    }
}