<?php

namespace App\Entity;

use App\Repository\ProductoEspacioRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: ProductoEspacioRepository::class)]
#[ApiResource]
class ProductoEspacio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cantidad;

    #[ORM\ManyToOne(targetEntity: Espacio::class, inversedBy: 'productosEspacio')]
    private $espacio;

    #[ORM\ManyToOne(targetEntity: Producto::class, inversedBy: 'productoEspacios')]
    private $producto;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(?int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getEspacio(): ?Espacio
    {
        return $this->espacio;
    }

    public function setEspacio(?Espacio $espacio): self
    {
        $this->espacio = $espacio;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

        return $this;
    }
}
