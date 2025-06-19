<?php

namespace SuperVMar\App\Controller\Product;

use JsonException;
use SuperVMar\Product\Application\Save\SaveProductCommand;
use SuperVMar\Shared\Domain\Bus\Command\CommandBus;
use SuperVMar\Shared\Domain\Bus\Query\QueryBus;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Shared\Infrastructure\Symfony\ApiExceptionHttpStatusCodeMapping;
use SuperVMar\Shared\Infrastructure\Symfony\ImageUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProductPostController extends ApiController
{
    public function __construct(
        QueryBus $queryBus,
        CommandBus $commandBus,
        ApiExceptionHttpStatusCodeMapping $exceptionHandler,
        private ImageUploader $imageUploader

    )
    {
        parent::__construct($queryBus, $commandBus, $exceptionHandler);
    }

    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request, true);

        $imageUrl = null;
        if ($request->files->has('image')) {
            $uploadedFile = $request->files->get('image');
            $imageUrl = $this->imageUploader->upload($uploadedFile, 'products');
        }

        $this->dispatch(
            new SaveProductCommand(
                $id,
                $data['data']['name'],
                $data['data']['price'],
                $data['data']['ean'],
                $data['data']['stock'],
                $data['data']['tax'],
                $data['data']['category'],
                $data['data']['supplier'],
                $data['data']['active'],
                $imageUrl
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['data'];
    }
}