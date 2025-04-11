<?php

namespace SuperVMar\App\Controller\Product;

use JsonException;
use SuperVMar\Product\Application\Save\SaveProductCommand;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProductPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new SaveProductCommand(
                $id,
                $data['name'],
                $data['price'],
                $data['ean'],
                $data['stock'],
                $data['tax'],
                $data['category'],
                $data['supplier'],
                $data['active'],
                $data['image'],
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['name', 'price', 'ean', 'stock', 'tax', 'category', 'supplier', 'active', 'image'];
    }
}