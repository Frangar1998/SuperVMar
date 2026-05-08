<?php

namespace SuperVMar\App\Controller\Product;

use SuperVMar\Product\Application\Search\Products\SearchProductsQuery;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Product\Application\Search\Products\ProductsResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ProductsGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        /**
         * @var ProductsResponse $response
         */
        $response = $this->ask(new SearchProductsQuery());

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}