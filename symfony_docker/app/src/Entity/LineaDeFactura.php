<?php

namespace App\Entity;

use App\Repository\LineaDeFacturaRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: LineaDeFacturaRepository::class)]
#[ApiResource]
class LineaDeFactura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $cantidad;

    #[ORM\Column(type: 'float')]
    private $importe;

    #[ORM\ManyToOne(targetEntity: Producto::class, inversedBy: 'lineasDeFactura')]
    private $productos;

    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'lineasDeFactura')]
    private $factura;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getImporte(): ?float
    {
        return $this->importe;
    }

    public function setImporte(float $importe): self
    {
        $this->importe = $importe;

        return $this;
    }

    public function getProductos(): ?Producto
    {
        return $this->productos;
    }

    public function setProductos(?Producto $productos): self
    {
        $this->productos = $productos;

        return $this;
    }

    public function getFactura(): ?Factura
    {
        return $this->factura;
    }

    public function setFactura(?Factura $factura): self
    {
        $this->factura = $factura;

        return $this;
    }
}
