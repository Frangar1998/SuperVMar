<?php

namespace SuperVMar\App\Controller\Supermarket;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Supermarket\Application\Search\Supermarket\SearchSupermarketsQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class SupermarketsGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $response = $this->ask(new SearchSupermarketsQuery());

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}

