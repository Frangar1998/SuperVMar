<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TiendaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TiendaRepository::class)]
#[ApiResource]
class Tienda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 255)]
    private $direccion;

    #[ORM\Column(type: 'string', length: 20)]
    private $telefono;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\OneToMany(mappedBy: 'tienda', targetEntity: Zona::class)]
    private $zonas;

    #[ORM\OneToMany(mappedBy: 'tienda', targetEntity: UsuarioTienda::class)]
    private $usuariosTienda;

    public function __construct()
    {
        $this->zonas = new ArrayCollection();
        $this->usuariosTienda = new ArrayCollection();
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

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Zona>
     */
    public function getZonas(): Collection
    {
        return $this->zonas;
    }

    public function addZona(Zona $zona): self
    {
        if (!$this->zonas->contains($zona)) {
            $this->zonas[] = $zona;
            $zona->setTienda($this);
        }

        return $this;
    }

    public function removeZona(Zona $zona): self
    {
        if ($this->zonas->removeElement($zona)) {
            // set the owning side to null (unless already changed)
            if ($zona->getTienda() === $this) {
                $zona->setTienda(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UsuarioTienda>
     */
    public function getUsuariosTienda(): Collection
    {
        return $this->usuariosTienda;
    }

    public function addUsuariosTienda(UsuarioTienda $usuariosTienda): self
    {
        if (!$this->usuariosTienda->contains($usuariosTienda)) {
            $this->usuariosTienda[] = $usuariosTienda;
            $usuariosTienda->setTienda($this);
        }

        return $this;
    }

    public function removeUsuariosTienda(UsuarioTienda $usuariosTienda): self
    {
        if ($this->usuariosTienda->removeElement($usuariosTienda)) {
            // set the owning side to null (unless already changed)
            if ($usuariosTienda->getTienda() === $this) {
                $usuariosTienda->setTienda(null);
            }
        }

        return $this;
    }
}
