<?php

namespace SuperVMar\App\Controller\Category;

use SuperVMar\Category\Application\Search\SearchCategoriesQuery;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class CategoriesGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $response = $this->ask(new SearchCategoriesQuery());

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}