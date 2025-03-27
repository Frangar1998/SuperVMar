<?php

namespace SuperVMar\App\Controller\User;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\User\Application\Search\Users\SearchUsersQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class UsersGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $response = $this->ask(new SearchUsersQuery());

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}