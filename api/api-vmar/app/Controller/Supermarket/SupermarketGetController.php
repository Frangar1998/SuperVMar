<?php

namespace SuperVMar\App\Controller\Supermarket;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Supermarket\Application\Search\Supermarket\SearchSupermarketQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class SupermarketGetController extends ApiController
{
    public function __invoke(string $id): JsonResponse
    {
        $response = $this->ask(new SearchSupermarketQuery($id));

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}