<?php

namespace SuperVMar\Product\Domain;

use SuperVMar\Product\Domain\Entity\Category;
use SuperVMar\Product\Domain\Entity\HistoricalPrice;
use SuperVMar\Product\Domain\Entity\PriceHistory;
use SuperVMar\Product\Domain\Entity\Supplier;
use SuperVMar\Product\Domain\Entity\Tax;
use SuperVMar\Product\Domain\ValueObject\Active;
use SuperVMar\Product\Domain\ValueObject\Ean;
use SuperVMar\Product\Domain\ValueObject\EndDate;
use SuperVMar\Product\Domain\ValueObject\Image;
use SuperVMar\Product\Domain\ValueObject\Percent;
use SuperVMar\Product\Domain\ValueObject\Price;
use SuperVMar\Product\Domain\ValueObject\StartDate;
use SuperVMar\Product\Domain\ValueObject\Status;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Shared\Domain\ValueObject\Uuid;

final class Product extends AggregateRoot
{
    public function __construct(
        private readonly Id           $id,
        private Name                  $name,
        private Price                 $price,
        private readonly Ean          $ean,
        private Stock                 $stock,
        private Tax                   $tax,
        private Category              $category,
        private readonly Supplier     $supplier,
        private Active                $active,
        private readonly PriceHistory $priceHistory,
        private ?Image                $image = null,
    )
    {
    }
    
    public function id(): Id
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function price(): Price
    {
        return $this->price;
    }

    public function ean(): Ean
    {
        return $this->ean;
    }

    public function stock(): Stock
    {
        return $this->stock;
    }

    public function tax(): Tax
    {
        return $this->tax;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function supplier(): Supplier
    {
        return $this->supplier;
    }

    public function active(): Active
    {
        return $this->active;
    }

    public function priceHistory(): PriceHistory
    {
        return $this->priceHistory;
    }

    public function image(): ?Image
    {
        return $this->image;
    }

    public function changeName(Name $name): void
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
        }
    }

    public function changePrice(Price $price): void
    {
        if (!$this->price->equals($price)) {
            $this->price = $price;
            $changeDate = date("Y-m-d H:i:s");
            $this->updatePriceHistory(new EndDate($changeDate));
            $this->createNewPrice(new StartDate($changeDate));
        }
    }

    private function updatePriceHistory(EndDate $endDate): void
    {
        /**
         * @var HistoricalPrice $currentPrice
         */
        $currentPrice = $this->priceHistory->first();
        $oldPrice = new HistoricalPrice(
            $currentPrice->id(),
            $currentPrice->price(),
            $currentPrice->startDate(),
            $endDate
        );
        $this->priceHistory->replace($oldPrice, 0);
    }

    public function createNewPrice(StartDate $startDate): void
    {
        $this->priceHistory->addFirst(
            new HistoricalPrice(
                new Id(Uuid::random()->value()),
                $this->price,
                $startDate
            )
        );
    }

    public function changeStock(Stock $stock): void
    {
        if (!$this->stock->equals($stock)) {
            $this->stock = $stock;
        }
    }

    public function addStock(Stock $addedStock): void
    {
        $newStock = $this->stock->add($addedStock);
        $this->stock = $newStock;
    }

    public function subtractStock(Stock $subtractedStock): void
    {
        $this->stock = $this->stock->subtract($subtractedStock);
    }

    public function changeTax(Tax $tax): void
    {
        if (!$this->tax->equals($tax)) {
            $this->tax = $tax;
        }
    }

    public function changeCategory(Category $category): void
    {
        if (!$this->category->equals($category)) {
            $this->category = $category;
        }
    }

    public function changeStatus(Active $active): void
    {
        if (!$this->active->equals($active)) {
            $this->active = $active;
        }
    }

    public function changeImage(?Image $image): void
    {
        if (!$this->image?->equals($image)) {
            $this->image = $image;
        }
    }

    public function checkIfCanDelete(ProductRepository $productRepository): void
    {
        try {
            $productRepository->checkAllocationExists($this->id);
            throw new CannotDeleteException('Cannot delete a product with an allocation.');
        } catch (ItemNotFoundException) {
            if ($this->active->value() !== Status::INACTIVE->value) {
                throw new CannotDeleteException('Cannot delete an active product.');
            }
            if ($this->stock()->value() > 0) {
                throw new CannotDeleteException('Cannot delete a product with stock.');
            }
        }
    }

    public static function create(
        Id $id,
        Name $name,
        Price $price,
        Ean $ean,
        Stock $stock,
        Tax $tax,
        Category $category,
        Supplier $supplier,
        Active $active,
        ?Image $image = null
    ): self
    {
        $product = new self(
            $id,
            $name,
            $price,
            $ean,
            $stock,
            $tax,
            $category,
            $supplier,
            $active,
            new PriceHistory(),
            $image
        );

        $product->createNewPrice(
            new StartDate(date("Y-m-d H:i:s"))
        );

        return $product;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Price($data['price']),
            new Ean($data['ean']),
            new Stock($data['stock']),
            Tax::fromArray([
                'id' => $data['idTax'],
                'name' => $data['nameTax'],
                'percent' => $data['percent'],
            ]),
            Category::fromArray([
                'id' => $data['idCategory'],
                'name' => $data['nameCategory'],
            ]),
            Supplier::fromArray([
                'id' => $data['idSupplier'],
                'name' => $data['nameSupplier'],
            ]),
            new Active($data['active']),
            PriceHistory::fromArray($data['priceHistory']),
            isset($data['image']) ? new Image($data['image']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'price' => $this->price->value(),
            'ean' => $this->ean->value(),
            'stock' => $this->stock->value(),
            'tax' => $this->tax->toArray(),
            'category' => $this->category->toArray(),
            'supplier' => $this->supplier->toArray(),
            'active' => $this->active->value(),
            'price_history' => $this->priceHistory->toArray(),
            'image' => $this->image?->value()
        ];
    }

    public function toTableData(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'price' => $this->price->value(),
            'ean' => $this->ean->value(),
            'stock' => $this->stock->value(),
            'tax' => $this->tax->nameValue(),
            'category' => $this->category->nameValue(),
            'active' => $this->active->value(),
            'image' => $this->image?->value()
        ];
    }
    
}