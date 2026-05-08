<?php

namespace SuperVMar\Shared\Infrastructure\Symfony;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageUploader
{
    public function __construct(
        private string $targetDirectory
    )
    {
    }

    public function upload(UploadedFile $file, string $type): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = sprintf("%s-%s.%s", $originalFilename, uniqid(), $file->guessExtension());
        $directory = $this->targetDirectory . $type;

        $file->move($directory, $filename);

        return sprintf("/images/%s/%s", $type, $filename);
    }
}