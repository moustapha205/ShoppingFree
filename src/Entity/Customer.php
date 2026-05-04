<?php

namespace App\Entity;// le chemin de l'entité, qui correspond au dossier dans lequel elle se trouve. Cela permet à Symfony de trouver et de charger cette classe correctement.

use App\Repository\CustomerRepository;//pour appeler le repository des clients et pouvoir faire des requetes en BDD pour les clients
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;//pour dire à doctrine comment mapper en BDD

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthDate = null; // pour dire que c'est une colonne en BDD de type date qui peut être null, ce qui signifie que la date de naissance d'un client est optionnelle et peut ne pas être fournie. Cela permet de gérer les cas où l'information de la date de naissance n'est pas disponible ou n'est pas souhaitée pour certains clients.

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'customers')]// plusieurs clients peuvent etre associés à une même marque, mais un client ne peut être associé qu'à une seule marque. L'attribut inversedBy indique que la relation inverse est définie dans l'entité Brand avec la propriété 'customers', ce qui permet de naviguer facilement entre les clients et les marques dans les deux sens.
    #[ORM\JoinColumn(nullable: true)]
    private ?Brand $brand = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;
        return $this;
    }
}
