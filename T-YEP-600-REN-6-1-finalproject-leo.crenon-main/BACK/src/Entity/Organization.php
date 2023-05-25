<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Assert\NotBlank
     */
    private $name;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Organization')]
    private $owner;

    #[ORM\OneToMany(mappedBy: 'idOrganization', targetEntity: UserInOrganization::class)]
    private $userInOrganization;

    #[ORM\OneToMany(mappedBy: 'idOrganization', targetEntity: Folder::class)]
    private Collection $folder;

    #[ORM\Column]
    private ?bool $visible = null;

    public function __construct()
    {
        $this->userInOrganization = new ArrayCollection();
        $this->folder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, UserInOrganization>
     */
    public function getUserInOrganization(): Collection
    {
        return $this->userInOrganization;
    }

    public function addUserInOrganization(UserInOrganization $userInOrganization): self
    {
        if (!$this->userInOrganization->contains($userInOrganization)) {
            $this->userInOrganization[] = $userInOrganization;
            $userInOrganization->setIdOrganization($this);
        }

        return $this;
    }

    public function removeUserInOrganization(UserInOrganization $userInOrganization): self
    {
        if ($this->userInOrganization->removeElement($userInOrganization)) {
            // set the owning side to null (unless already changed)
            if ($userInOrganization->getIdOrganization() === $this) {
                $userInOrganization->setIdOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<Folder>
     */
    public function getFolder(): Collection
    {
        return $this->folder;
    }

    public function addFolder(Folder $folder): self
    {
        if (!$this->folder->contains($folder)) {
            $this->folder[] = $folder;
            $folder->setIdOrganization($this);
        }

        return $this;
    }

    public function removeFolder(Folder $folder): self
    {
        if ($this->folder->removeElement($folder)) {
            // set the owning side to null (unless already changed)
            if ($folder->getIdOrganization() === $this) {
                $folder->setIdOrganization(null);
            }
        }

        return $this;
    }


    public function toString() {
        return array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "owner" => $this->getOwner()->toString(),

        );
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }
}
