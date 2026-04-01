<?php

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomBrand = null;

    #[ORM\Column(length: 20)]
    private ?string $telBrand = null;

    #[ORM\Column(length: 20)]
    private ?string $siretBrand = null;

    #[ORM\Column(length: 100)]
    private ?string $imageBrand = null;

    #[ORM\Column(length: 100)]
    private ?string $siteWebBrand = null;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Customer::class)]
    private Collection $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBrand(): ?string
    {
        return $this->nomBrand;
    }

    public function setNomBrand(string $nomBrand): static
    {
        $this->nomBrand = $nomBrand;
        return $this;
    }

    public function getTelBrand(): ?string
    {
        return $this->telBrand;
    }

    public function setTelBrand(string $telBrand): static
    {
        $this->telBrand = $telBrand;
        return $this;
    }

    public function getSiretBrand(): ?string
    {
        return $this->siretBrand;
    }

    public function setSiretBrand(string $siretBrand): static
    {
        $this->siretBrand = $siretBrand;
        return $this;
    }

    public function getImageBrand(): ?string
    {
        return $this->imageBrand;
    }

    public function setImageBrand(string $imageBrand): static
    {
        $this->imageBrand = $imageBrand;
        return $this;
    }

    public function getSiteWebBrand(): ?string
    {
        return $this->siteWebBrand;
    }

    public function setSiteWebBrand(string $siteWebBrand): static
    {
        $this->siteWebBrand = $siteWebBrand;
        return $this;
    }

    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): static
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setBrand($this);
        }
        return $this;
    }

    public function removeCustomer(Customer $customer): static
    {
        if ($this->customers->removeElement($customer)) {
            if ($customer->getBrand() === $this) {
                $customer->setBrand(null);
            }
        }
        return $this;
    }
}
