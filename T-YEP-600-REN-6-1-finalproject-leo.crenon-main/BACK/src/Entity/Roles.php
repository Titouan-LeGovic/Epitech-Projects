<?php

namespace App\Entity;

use App\Repository\RolesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
class Roles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Folder::class, inversedBy: 'roles')]
    private Folder $idFolder;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'roles')]
    private User $idUser;

    #[ORM\Column(length: 4)]
    private string $Value = "0000";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFolder(): ?Folder
    {
        return $this->idFolder;
    }

    public function setIdFolder(?Folder $idFolder): self
    {
        $this->idFolder = $idFolder;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->Value;
    }

    public function setValue(string $Value): self
    {
        $this->Value = $Value;

        return $this;
    }

    public function toString() {
        return array(
            "id" => $this->getId(),
            "value" => $this->getValue(),
        );
        // "id_user" => $this->getIdUser()->toString(),
        // "id_folder" => $this->getIdFolder()->toString(),
    }
}
