<?php

namespace App\Entity;

use App\Repository\ZonaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: ZonaRepository::class)]
#[ApiResource]
class Zona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_x0;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_y0;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_x1;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_y1;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_x2;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_y2;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_x3;

    #[ORM\Column(type: 'integer')]
    private $boundingBox_y3;

    #[ORM\OneToMany(mappedBy: 'zona', targetEntity: Espacio::class)]
    private $espacios;

    #[ORM\ManyToOne(targetEntity: Tienda::class, inversedBy: 'zonas')]
    private $tienda;

    public function __construct()
    {
        $this->espacios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getBoundingBoxX0(): ?int
    {
        return $this->boundingBox_x0;
    }

    public function setBoundingBoxX0(int $boundingBox_x0): self
    {
        $this->boundingBox_x0 = $boundingBox_x0;

        return $this;
    }

    public function getBoundingBoxY0(): ?int
    {
        return $this->boundingBox_y0;
    }

    public function setBoundingBoxY0(int $boundingBox_y0): self
    {
        $this->boundingBox_y0 = $boundingBox_y0;

        return $this;
    }

    public function getBoundingBoxX1(): ?int
    {
        return $this->boundingBox_x1;
    }

    public function setBoundingBoxX1(int $boundingBox_x1): self
    {
        $this->boundingBox_x1 = $boundingBox_x1;

        return $this;
    }

    public function getBoundingBoxY1(): ?int
    {
        return $this->boundingBox_y1;
    }

    public function setBoundingBoxY1(int $boundingBox_y1): self
    {
        $this->boundingBox_y1 = $boundingBox_y1;

        return $this;
    }

    public function getBoundingBoxX2(): ?int
    {
        return $this->boundingBox_x2;
    }

    public function setBoundingBoxX2(int $boundingBox_x2): self
    {
        $this->boundingBox_x2 = $boundingBox_x2;

        return $this;
    }

    public function getBoundingBoxY2(): ?int
    {
        return $this->boundingBox_y2;
    }

    public function setBoundingBoxY2(int $boundingBox_y2): self
    {
        $this->boundingBox_y2 = $boundingBox_y2;

        return $this;
    }

    public function getBoundingBoxX3(): ?int
    {
        return $this->boundingBox_x3;
    }

    public function setBoundingBoxX3(int $boundingBox_x3): self
    {
        $this->boundingBox_x3 = $boundingBox_x3;

        return $this;
    }

    public function getBoundingBoxY3(): ?int
    {
        return $this->boundingBox_y3;
    }

    public function setBoundingBoxY3(int $boundingBox_y3): self
    {
        $this->boundingBox_y3 = $boundingBox_y3;

        return $this;
    }

    /**
     * @return Collection<int, Espacio>
     */
    public function getEspacios(): Collection
    {
        return $this->espacios;
    }

    public function addEspacio(Espacio $espacio): self
    {
        if (!$this->espacios->contains($espacio)) {
            $this->espacios[] = $espacio;
            $espacio->setZona($this);
        }

        return $this;
    }

    public function removeEspacio(Espacio $espacio): self
    {
        if ($this->espacios->removeElement($espacio)) {
            // set the owning side to null (unless already changed)
            if ($espacio->getZona() === $this) {
                $espacio->setZona(null);
            }
        }

        return $this;
    }

    public function getTienda(): ?Tienda
    {
        return $this->tienda;
    }

    public function setTienda(?Tienda $tienda): self
    {
        $this->tienda = $tienda;

        return $this;
    }
}
