<?php

namespace SuperVMar\App\Controller\Sale;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\src\Sale\Application\Search\Sale\SearchSaleQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class SaleGetController extends ApiController
{
    public function __invoke(string $id): JsonResponse
    {
        $response = $this->ask(new SearchSaleQuery($id));

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}