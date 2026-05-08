<?php

namespace SuperVMar\App\Controller\Category;

use SuperVMar\Category\Application\Delete\DeleteCategoryCommand;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final readonly class CategoryDeleteController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $this->dispatch(new DeleteCategoryCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}