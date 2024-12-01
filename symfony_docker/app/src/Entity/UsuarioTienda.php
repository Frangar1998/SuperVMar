<?php

namespace App\Entity;

use App\Repository\UsuarioTiendaRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: UsuarioTiendaRepository::class)]
#[ApiResource]
class UsuarioTienda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $tipoUsuario;

    #[ORM\ManyToOne(targetEntity: Tienda::class, inversedBy: 'usuariosTienda')]
    private $tienda;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'usuarioTiendas')]
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipoUsuario(): ?string
    {
        return $this->tipoUsuario;
    }

    public function setTipoUsuario(string $tipoUsuario): self
    {
        $this->tipoUsuario = $tipoUsuario;

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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}
