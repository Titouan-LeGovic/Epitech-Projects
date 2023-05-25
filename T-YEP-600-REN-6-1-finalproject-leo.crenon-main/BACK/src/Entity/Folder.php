<?php

namespace App\Entity;

use App\Repository\FolderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FolderRepository::class)]
class Folder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'rules')]
    private $idOrganization;

    #[ORM\OneToMany(mappedBy: 'idFolder', targetEntity: Rule::class)]
    private Collection $rules;

    #[ORM\OneToMany(mappedBy: 'idFolder', targetEntity: Document::class)]
    private Collection $Document;

    #[ORM\OneToMany(mappedBy: 'idFolder', targetEntity: Roles::class)]
    private Collection $roles;

    // #[ORM\Column(nullable: true)]
    // private ?int $icon = null;

    #[ORM\Column]
    private ?bool $visible = null;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->Document = new ArrayCollection();
        $this->roles = new ArrayCollection();
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

    public function getIdOrganization(): ?Organization
    {
        return $this->idOrganization;
    }

    public function setIdOrganization(?Organization $idOrganization): self
    {
        $this->idOrganization = $idOrganization;

        return $this;
    }
    
    /**
     * @return Collection<int, Roles>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRoles(Roles $roles): self
    {
        if (!$this->roles->contains($roles)) {
            $this->roles[] = $roles;
            $roles->setIdFolder($this);
        }

        return $this;
    }

    public function removeRoles(Roles $roles): self
    {
        if ($this->roles->removeElement($roles)) {
            // set the owning side to null (unless already changed)
            if ($roles->getIdFolder() === $this) {
                $roles->setIdFolder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rules>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(Rule $rule): self
    {
        if (!$this->rules->contains($rule)) {
            $this->rules[] = $rule;
            $rule->setIdFolder($this);
        }

        return $this;
    }

    public function removeRule(Rule $rule): self
    {
        if ($this->rules->removeElement($rule)) {
            // set the owning side to null (unless already changed)
            if ($rule->getIdFolder() === $this) {
                $rule->setIdFolder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocument(): Collection
    {
        return $this->Document;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->Document->contains($document)) {
            $this->Document[] = $document;
            $document->setIdFolder($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->Document->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getIdFolder() === $this) {
                $document->setIdFolder(null);
            }
        }

        return $this;
    }

    public function toString() {
        $docs = [];
        for($d=0;$d<sizeof($this->getDocument());$d++)
        {
            array_push($docs, $this->getDocument()[$d]->toString());
        }
        $rules = [];
        for($r=0;$r<sizeof($this->getRules());$r++)
        {
            array_push($rules, $this->getRules()[$r]->toString());
        }
        return array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "rules" => $rules,
            "documents" => $docs,
        );
        //"organization" => $this->getIdOrganization()->toString(),
        // "icon" => $this->getIcon(),
    }

    public function getIcon(): ?int
    {
        return $this->icon;
    }

    public function setIcon(?int $icon): self
    {
         $this->icon = $icon;

        return $this;
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
