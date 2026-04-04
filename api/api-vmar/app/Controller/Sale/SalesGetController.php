<?php

namespace SuperVMar\App\Controller\Sale;

use SuperVMar\Sale\Application\Search\Sales\SearchSalesQuery;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class SalesGetController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $date = $request->query->get('date');

        $response = $this->ask(new SearchSalesQuery($date));

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}