<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
#[ApiResource]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 255)]
    private $ean;

    #[ORM\Column(type: 'float')]
    private $precio;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $fresco;

    #[ORM\ManyToOne(targetEntity: TipoImpositivo::class, inversedBy: 'productos')]
    private $tipoIva;

    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: HistorialPrecio::class)]
    private $historialPrecios;

    #[ORM\ManyToOne(targetEntity: Proveedor::class, inversedBy: 'productos')]
    private $proveedor;

    #[ORM\OneToMany(mappedBy: 'productos', targetEntity: LineaDeFactura::class)]
    private $lineasDeFactura;

    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: ProductoEspacio::class)]
    private $productoEspacios;

    public function __construct($nombre, $ean, $precio, $fresco)
    {
        $this->nombre = $nombre;
        $this->ean = $ean;
        $this->precio = $precio;
        $this->fresco = $fresco;
        $this->historialPrecios = new ArrayCollection();
        $this->lineasDeFactura = new ArrayCollection();
        $this->productoEspacios = new ArrayCollection();
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

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(string $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getFresco(): ?bool
    {
        return $this->fresco;
    }

    public function setFresco(?bool $fresco): self
    {
        $this->fresco = $fresco;

        return $this;
    }

    public function getTipoIva(): ?TipoImpositivo
    {
        return $this->tipoIva;
    }

    public function setTipoIva(?TipoImpositivo $tipoIva): self
    {
        $this->tipoIva = $tipoIva;

        return $this;
    }

    /**
     * @return Collection<int, HistorialPrecio>
     */
    public function getHistorialPrecios(): Collection
    {
        return $this->historialPrecios;
    }

    public function addHistorialPrecio(HistorialPrecio $historialPrecio): self
    {
        if (!$this->historialPrecios->contains($historialPrecio)) {
            $this->historialPrecios[] = $historialPrecio;
            $historialPrecio->setProducto($this);
        }

        return $this;
    }

    public function removeHistorialPrecio(HistorialPrecio $historialPrecio): self
    {
        if ($this->historialPrecios->removeElement($historialPrecio)) {
            // set the owning side to null (unless already changed)
            if ($historialPrecio->getProducto() === $this) {
                $historialPrecio->setProducto(null);
            }
        }

        return $this;
    }

    public function getProveedor(): ?Proveedor
    {
        return $this->proveedor;
    }

    public function setProveedor(?Proveedor $proveedor): self
    {
        $this->proveedor = $proveedor;

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
            $lineasDeFactura->setProductos($this);
        }

        return $this;
    }

    public function removeLineasDeFactura(LineaDeFactura $lineasDeFactura): self
    {
        if ($this->lineasDeFactura->removeElement($lineasDeFactura)) {
            // set the owning side to null (unless already changed)
            if ($lineasDeFactura->getProductos() === $this) {
                $lineasDeFactura->setProductos(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductoEspacio>
     */
    public function getProductoEspacios(): Collection
    {
        return $this->productoEspacios;
    }

    public function addProductoEspacio(ProductoEspacio $productoEspacio): self
    {
        if (!$this->productoEspacios->contains($productoEspacio)) {
            $this->productoEspacios[] = $productoEspacio;
            $productoEspacio->setProducto($this);
        }

        return $this;
    }

    public function removeProductoEspacio(ProductoEspacio $productoEspacio): self
    {
        if ($this->productoEspacios->removeElement($productoEspacio)) {
            // set the owning side to null (unless already changed)
            if ($productoEspacio->getProducto() === $this) {
                $productoEspacio->setProducto(null);
            }
        }

        return $this;
    }
}
