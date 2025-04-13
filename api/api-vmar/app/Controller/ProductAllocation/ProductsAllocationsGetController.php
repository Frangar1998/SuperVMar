<?php

namespace SuperVMar\App\Controller\ProductAllocation;

use SuperVMar\ProductAllocation\Application\Search\SearchProductsAllocationsQuery;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ProductsAllocationsGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $response = $this->ask(new SearchProductsAllocationsQuery());

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}