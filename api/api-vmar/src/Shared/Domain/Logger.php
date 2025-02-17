<?php

namespace SuperVMar\Shared\Domain;

interface Logger
{
    public function info(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;
}