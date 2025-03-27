<?php

namespace SuperVMar\App\Controller\User;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\User\Application\Search\User\SearchUserQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class UserGetController extends ApiController
{
    public function __invoke(string $id): JsonResponse
    {
        $response = $this->ask(new SearchUserQuery($id));

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}