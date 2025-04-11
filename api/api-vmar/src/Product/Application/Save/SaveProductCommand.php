<?php

namespace SuperVMar\Product\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveProductCommand implements Command
{
    public function __construct(
        private string  $id,
        private string  $name,
        private float   $price,
        private string  $ean,
        private int     $stock,
        private array   $tax,
        private array   $category,
        private array   $supplier,
        private int     $active,
        private ?string $image = null,
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function ean(): string
    {
        return $this->ean;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function tax(): array
    {
        return $this->tax;
    }

    public function category(): array
    {
        return $this->category;
    }

    public function supplier(): array
    {
        return $this->supplier;
    }

    public function active(): int
    {
        return $this->active;
    }

    public function image(): ?string
    {
        return $this->image;
    }
    
    
}