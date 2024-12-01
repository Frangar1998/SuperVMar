<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ApiResource]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $username;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\OneToOne(targetEntity: InfoUsuario::class, cascade: ['persist', 'remove'])]
    private $infoUsuario;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: UsuarioTienda::class)]
    private $usuarioTiendas;

    public function __construct()
    {
        $this->usuarioTiendas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getInfoUsuario(): ?InfoUsuario
    {
        return $this->infoUsuario;
    }

    public function setInfoUsuario(?InfoUsuario $infoUsuario): self
    {
        $this->infoUsuario = $infoUsuario;

        return $this;
    }

    /**
     * @return Collection<int, UsuarioTienda>
     */
    public function getUsuarioTiendas(): Collection
    {
        return $this->usuarioTiendas;
    }

    public function addUsuarioTienda(UsuarioTienda $usuarioTienda): self
    {
        if (!$this->usuarioTiendas->contains($usuarioTienda)) {
            $this->usuarioTiendas[] = $usuarioTienda;
            $usuarioTienda->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioTienda(UsuarioTienda $usuarioTienda): self
    {
        if ($this->usuarioTiendas->removeElement($usuarioTienda)) {
            // set the owning side to null (unless already changed)
            if ($usuarioTienda->getUsuario() === $this) {
                $usuarioTienda->setUsuario(null);
            }
        }

        return $this;
    }
}
