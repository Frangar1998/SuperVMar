<?php

namespace SuperVMar\App\Controller\Category;

use JsonException;
use SuperVMar\Category\Application\Save\SaveCategoryCommand;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CategoryPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new SaveCategoryCommand(
                $id,
                $data['name']
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['name'];
    }
}