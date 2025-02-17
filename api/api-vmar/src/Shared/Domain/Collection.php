<?php

namespace SuperVMar\Shared\Domain;

use Countable;
use IteratorAggregate;
use Traversable;
use ArrayIterator;

abstract class Collection implements Countable, IteratorAggregate
{
    public function __construct(protected array $items = [])
    {
        Assert::arrayOf($this->type(), $this->items);
    }

    final public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    final public function count(): int
    {
        return count($this->items);
    }

    protected function items(): array
    {
        return $this->items;
    }

    public function add(mixed $item): void
    {
        $this->items[] = $item;
    }
    public function remove(mixed $itemToRemove): void
    {
        foreach ($this->getIterator() as $key => $item) {
            if ($item === $itemToRemove) {
                unset($this->items[$key]);
            }
        }
    }

    abstract protected function type(): string;

    public function toArray(): array
    {
        return array_map(
            fn($item) => $item->toArray(),
            $this->items
        );
    }
}