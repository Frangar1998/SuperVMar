<?php

namespace SuperVMar\App\Controller\User;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\User\Application\Delete\DeleteUserCommand;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserDeleteController extends ApiController
{

    public function __invoke(string $id): Response
    {
        $this->dispatch(new DeleteUserCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}