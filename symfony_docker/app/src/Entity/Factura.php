<?php

namespace App\Entity;

use App\Repository\FacturaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: FacturaRepository::class)]
#[ApiResource]
class Factura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $fecha;

    #[ORM\Column(type: 'float')]
    private $importeTotal;

    #[ORM\Column(type: 'float')]
    private $importeSinIva;

    #[ORM\Column(type: 'float')]
    private $importeIva;

    #[ORM\OneToMany(mappedBy: 'factura', targetEntity: LineaDeFactura::class)]
    private $lineasDeFactura;

    #[ORM\ManyToOne(targetEntity: Cliente::class, inversedBy: 'facturas')]
    private $cliente;

    public function __construct()
    {
        $this->lineasDeFactura = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getImporteTotal(): ?float
    {
        return $this->importeTotal;
    }

    public function setImporteTotal(float $importeTotal): self
    {
        $this->importeTotal = $importeTotal;

        return $this;
    }

    public function getImporteSinIva(): ?float
    {
        return $this->importeSinIva;
    }

    public function setImporteSinIva(float $importeSinIva): self
    {
        $this->importeSinIva = $importeSinIva;

        return $this;
    }

    public function getImporteIva(): ?float
    {
        return $this->importeIva;
    }

    public function setImporteIva(float $importeIva): self
    {
        $this->importeIva = $importeIva;

        return $this;
    }

    /**
     * @return Collection<int, LineaDeFactura>
     */
    public function getLineasDeFactura(): Collection
    {
        return $this->lineasDeFactura;
    }

    public function addLineasDeFactura(LineaDeFactura $lineasDeFactura): self
    {
        if (!$this->lineasDeFactura->contains($lineasDeFactura)) {
            $this->lineasDeFactura[] = $lineasDeFactura;
            $lineasDeFactura->setFactura($this);
        }

        return $this;
    }

    public function removeLineasDeFactura(LineaDeFactura $lineasDeFactura): self
    {
        if ($this->lineasDeFactura->removeElement($lineasDeFactura)) {
            // set the owning side to null (unless already changed)
            if ($lineasDeFactura->getFactura() === $this) {
                $lineasDeFactura->setFactura(null);
            }
        }

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }
}
