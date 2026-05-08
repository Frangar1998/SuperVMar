<?php

namespace SuperVMar\App\Controller\Product;

use SuperVMar\Product\Application\Search\Product\SearchProductQuery;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class ProductGetController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $field = $request->query->get('field');
        $value = $request->query->get('value');
        $response = $this->ask(new SearchProductQuery($field, $value));

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}