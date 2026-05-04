<?php

namespace App\Entity;// le chemin de l'entité, qui correspond au dossier dans lequel elle se trouve. Cela permet à Symfony de trouver et de charger cette classe correctement.

use App\Repository\BrandRepository; //pour appeler le repository des marques et pouvoir faire des requetes en BDD pour les marques
use Doctrine\Common\Collections\ArrayCollection;//pour appeler et utiliser la liste des objets lies à une entité
use Doctrine\Common\Collections\Collection;//
use Doctrine\ORM\Mapping as ORM;//pour dire à doctrine comment mapper en BDD

#[ORM\Entity(repositoryClass: BrandRepository::class)]// pour dire que cette classe est une entité Doctrine et que son repository associé est BrandRepository, ce qui nous permettra de faire des requêtes spécifiques sur les marques en utilisant ce repository.
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
    private Collection $customers; // pour dire que c'est une relation OneToMany avec l'entité Customer, ce qui signifie qu'une marque peut être associée à plusieurs clients, mais un client ne peut être associé qu'à une seule marque. L'attribut mappedBy indique que la relation est définie dans l'entité Customer avec la propriété 'brand', ce qui permet de naviguer facilement entre les marques et les clients dans les deux sens.

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
        if (!$this->customers->contains($customer)) {//"Si ce customer client N'EST PAS déjà dans la liste des customers de cette marque alors..."
            $this->customers->add($customer);// ajoutons ce customer dans la liste des clients de cette marque 
            $customer->setBrand($this);// et là on communique au customer que sa marque c'est cette marque là, pour que la relation soit bien établie dans les deux sens (dans la marque et dans le client)
        }
        return $this;
    }

    public function removeCustomer(Customer $customer): static
    {
        if ($this->customers->removeElement($customer)) { // si dans la liste des customer d'une marque on supprime ou retire un customer client alors 
            if ($customer->getBrand() === $this) { //si ce customer retiré faisait parti des clients de cette marque alors...
                $customer->setBrand(null);//on communique au costumer qu'il n'a plus de marque qui lui est associée, pour que la relation soit bien supprimée dans les deux sens (dans la marque et dans le client)
            }
        }
        return $this;
    }
}
