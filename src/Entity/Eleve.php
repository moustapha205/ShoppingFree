<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EleveRepository::class)]
class Eleve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomEleve = null;

    #[ORM\Column(length: 20)]
    private ?string $telEleve = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $naissanceEleve = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\ManyToOne(targetEntity: AutoEcole::class, inversedBy: 'eleves')]
    #[ORM\JoinColumn(nullable: true)]
    private ?AutoEcole $autoEcole = null;

    #[ORM\Column(length: 50)]
    private ?string $prenomEleve = null;

    // getters & setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEleve(): ?string
    {
        return $this->nomEleve;
    }

    public function setNomEleve(string $nomEleve): static
    {
        $this->nomEleve = $nomEleve;

        return $this;
    }

    public function getTelEleve(): ?string
    {
        return $this->telEleve;
    }

    public function setTelEleve(string $telEleve): static
    {
        $this->telEleve = $telEleve;

        return $this;
    }

    public function getNaissanceEleve(): ?\DateTime
    {
        return $this->naissanceEleve;
    }

    public function setNaissanceEleve(?\DateTime $naissanceEleve): static
    {
        $this->naissanceEleve = $naissanceEleve;

        return $this;
    }

    public function getDateInscription(): ?\DateTime
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTime $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getAutoEcole(): ?AutoEcole
    {
        return $this->autoEcole;
    }

    public function setAutoEcole(?AutoEcole $autoEcole): static
    {
        $this->autoEcole = $autoEcole;

        return $this;
    }

    public function getPrenomEleve(): ?string
    {
        return $this->prenomEleve;
    }

    public function setPrenomEleve(string $prenomEleve): static
    {
        $this->prenomEleve = $prenomEleve;

        return $this;
    }
}
