<?php

namespace SuperVMar\App\Controller\Sale;

use SuperVMar\Sale\Application\Search\SearchSaleQuery;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
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