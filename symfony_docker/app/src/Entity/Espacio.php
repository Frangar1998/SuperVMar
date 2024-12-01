<?php

namespace App\Entity;

use App\Repository\EspacioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: EspacioRepository::class)]
#[ApiResource]
class Espacio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $x0;

    #[ORM\Column(type: 'integer')]
    private $y0;

    #[ORM\Column(type: 'integer')]
    private $z0;

    #[ORM\Column(type: 'boolean')]
    private $esOferta;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $porcentajeOferta;

    #[ORM\OneToMany(mappedBy: 'espacio', targetEntity: ProductoEspacio::class)]
    private $productosEspacio;

    #[ORM\ManyToOne(targetEntity: Zona::class, inversedBy: 'espacios')]
    private $zona;

    public function __construct()
    {
        $this->productosEspacio = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getX0(): ?int
    {
        return $this->x0;
    }

    public function setX0(int $x0): self
    {
        $this->x0 = $x0;

        return $this;
    }

    public function getY0(): ?int
    {
        return $this->y0;
    }

    public function setY0(int $y0): self
    {
        $this->y0 = $y0;

        return $this;
    }

    public function getZ0(): ?int
    {
        return $this->z0;
    }

    public function setZ0(int $z0): self
    {
        $this->z0 = $z0;

        return $this;
    }

    public function getEsOferta(): ?bool
    {
        return $this->esOferta;
    }

    public function setEsOferta(bool $esOferta): self
    {
        $this->esOferta = $esOferta;

        return $this;
    }

    public function getPorcentajeOferta(): ?int
    {
        return $this->porcentajeOferta;
    }

    public function setPorcentajeOferta(?int $porcentajeOferta): self
    {
        $this->porcentajeOferta = $porcentajeOferta;

        return $this;
    }

    /**
     * @return Collection<int, ProductoEspacio>
     */
    public function getProductosEspacio(): Collection
    {
        return $this->productosEspacio;
    }

    public function addProductosEspacio(ProductoEspacio $productosEspacio): self
    {
        if (!$this->productosEspacio->contains($productosEspacio)) {
            $this->productosEspacio[] = $productosEspacio;
            $productosEspacio->setEspacio($this);
        }

        return $this;
    }

    public function removeProductosEspacio(ProductoEspacio $productosEspacio): self
    {
        if ($this->productosEspacio->removeElement($productosEspacio)) {
            // set the owning side to null (unless already changed)
            if ($productosEspacio->getEspacio() === $this) {
                $productosEspacio->setEspacio(null);
            }
        }

        return $this;
    }

    public function getZona(): ?Zona
    {
        return $this->zona;
    }

    public function setZona(?Zona $zona): self
    {
        $this->zona = $zona;

        return $this;
    }
}
