<?php

namespace SuperVMar\App\Controller\Sale;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\src\Sale\Application\Search\Sales\SearchSalesQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class SalesGetController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->dataFromRequest($request);

        $response = $this->ask(new SearchSalesQuery($data['date']));

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}