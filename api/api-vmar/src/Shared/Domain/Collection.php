<?php

namespace SuperVMar\Shared\Domain;

use Countable;
use IteratorAggregate;
use Traversable;
use ArrayIterator;

abstract class Collection implements Countable, IteratorAggregate
{
    protected array $addedItems = [];
    protected array $removedItems = [];
    protected array $replacedItems = [];

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

    public function items(): array
    {
        return $this->items;
    }

    public function addedItems(): array
    {
        return $this->addedItems;
    }

    public function removedItems(): array
    {
        return $this->removedItems;
    }

    public function replacedItems(): array
    {
        return $this->replacedItems;
    }

    public function add(mixed $item): void
    {
        Assert::instanceOf($this->type(), $item);
        $this->items[] = $item;
        $this->addedItems[] = $item;
    }

    public function remove(mixed $itemToRemove): void
    {
        Assert::instanceOf($this->type(), $itemToRemove);
        foreach ($this->items as $key => $item) {
            if ($item->equals($itemToRemove)) {
                unset($this->items[$key]);
                $this->removedItems[] = $itemToRemove;
            }
        }
    }

    public function replace(mixed $itemToReplace, int $key): void
    {
        Assert::instanceOf($this->type(), $itemToReplace);
        if (!$this->items[$key]->compare($itemToReplace)) {
            $this->items[$key] = $itemToReplace;
            $this->replacedItems[] = $itemToReplace;
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

    public function empty(): bool
    {
        return empty($this->items);
    }

    public function find(mixed $itemSearched): mixed
    {
        Assert::instanceOf($this->type(), $itemSearched);
        return array_find_key(
            $this->items,
            fn(mixed $item) => $item->equals($itemSearched)
        );
    }

    public function first(): mixed
    {
        return $this->items[0];
    }

}