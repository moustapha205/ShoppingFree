<?php

namespace App\Entity;

use App\Repository\AutoEcoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutoEcoleRepository::class)]
class AutoEcole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomAutoEcole = null;

    #[ORM\Column(length: 20)]
    private ?string $telAutoEcole = null;

    #[ORM\Column(length: 20)]
    private ?string $siretAutoEcole = null;

    #[ORM\Column(length: 100)]
    private ?string $imageAutoEcole = null;

    #[ORM\Column(length: 100)]
    private ?string $lienWebAutoEcole = null;

    #[ORM\OneToMany(mappedBy: 'autoEcole', targetEntity: Eleve::class)]
    private Collection $eleves;

    public function __construct()
    {
        $this->eleves = new ArrayCollection();
    }

    // getters & setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAutoEcole(): ?string
    {
        return $this->nomAutoEcole;
    }

    public function setNomAutoEcole(string $nomAutoEcole): static
    {
        $this->nomAutoEcole = $nomAutoEcole;

        return $this;
    }

    public function getTelAutoEcole(): ?string
    {
        return $this->telAutoEcole;
    }

    public function setTelAutoEcole(string $telAutoEcole): static
    {
        $this->telAutoEcole = $telAutoEcole;

        return $this;
    }

    public function getSiretAutoEcole(): ?string
    {
        return $this->siretAutoEcole;
    }

    public function setSiretAutoEcole(string $siretAutoEcole): static
    {
        $this->siretAutoEcole = $siretAutoEcole;

        return $this;
    }

    public function getImageAutoEcole(): ?string
    {
        return $this->imageAutoEcole;
    }

    public function setImageAutoEcole(string $imageAutoEcole): static
    {
        $this->imageAutoEcole = $imageAutoEcole;

        return $this;
    }

    public function getLienWebAutoEcole(): ?string
    {
        return $this->lienWebAutoEcole;
    }

    public function setLienWebAutoEcole(string $lienWebAutoEcole): static
    {
        $this->lienWebAutoEcole = $lienWebAutoEcole;

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): static
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves->add($elefe);
            $elefe->setAutoEcole($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): static
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getAutoEcole() === $this) {
                $elefe->setAutoEcole(null);
            }
        }

        return $this;
    }
}
