<?php

namespace SuperVMar\Sale\Domain\Entity;

use SuperVMar\Sale\Domain\ValueObject\Amount;
use SuperVMar\Shared\Domain\Assert;
use SuperVMar\Shared\Domain\Collection;

final class Lines extends Collection
{
    protected function type(): string
    {
        return Line::class;
    }

    public static function fromArray(array $lines): self
    {
        return new self(
            array_map(
                fn(array $line) => Line::fromArray($line),
                $lines
            )
        );
    }

    public function hasProduct(Product $product): mixed
    {
        /**
         * @var Line $line
         */
        return array_find_key(
            $this->items(),
            fn($line) => $line->product()->equals($product)
        );
    }

    public function replace(mixed $itemToReplace, int $key): void
    {
        Assert::instanceOf($this->type(), $itemToReplace);
        $this->items[$key] = $itemToReplace;
        $this->replacedItems[] = $itemToReplace;
    }
}