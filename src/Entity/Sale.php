<?php

namespace App\Entity;

use App\Repository\SaleRepository; //pour appeler le repository des ventes et pouvoir faire des requetes en BDD pour les ventes
use Doctrine\DBAL\Types\Types;//pour utiliser les types de données spécifiques de Doctrine, comme DECIMAL et TEXT, qui ne sont pas des types PHP natifs. Cela permet de définir précisément comment les données seront stockées en base de données. Par exemple, Types::DECIMAL indique que la colonne doit être traitée comme un nombre décimal avec une précision et une échelle spécifiques, tandis que Types::TEXT indique que la colonne doit être traitée comme un texte long.
use Doctrine\ORM\Mapping as ORM;//pour dire à doctrine comment mapper en BDD

#[ORM\Entity(repositoryClass: SaleRepository::class)] // pour dire que cette classe est une entité Doctrine et que son repository associé est SaleRepository, ce qui nous permettra de faire des requêtes spécifiques sur les ventes en utilisant ce repository.
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $productSku = null;

    #[ORM\Column]
    private ?int $quantitySold = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalTtc = null;

    #[ORM\ManyToOne(inversedBy: 'sales')] // pour dire que c'est une relation ManyToOne avec l'entité User, ce qui signifie qu'une vente est associée à un seul client (utilisateur), mais un client peut avoir plusieurs ventes. L'attribut inversedBy indique que la relation inverse est définie dans l'entité User avec la propriété 'sales', ce qui permet de naviguer facilement entre les ventes et les clients dans les deux sens.
    #[ORM\JoinColumn(nullable: false)] // pour dire que la colonne de jointure qui relie la vente au client ne peut pas être null, ce qui signifie qu'une vente doit toujours être associée à un client. Cela garantit l'intégrité des données en empêchant la création de ventes sans client associé.
    private ?user $client = null;  // pour dire que une vente appartient à un client (utilisateur) et que cette relation est obligatoire (non nullable). Cela signifie que chaque vente doit être associée à un client, et que la propriété $client peut être null avant d'être enregistrée en base de données, mais pas après.

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductSku(): ?string
    {
        return $this->productSku;
    }

    public function setProductSku(string $productSku): static
    {
        $this->productSku = $productSku;

        return $this;
    }

    public function getQuantitySold(): ?int
    {
        return $this->quantitySold;
    }

    public function setQuantitySold(int $quantitySold): static
    {
        $this->quantitySold = $quantitySold;

        return $this;
    }

    public function getTotalTtc(): ?string
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(string $totalTtc): static
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    public function getClient(): ?user
    {
        return $this->client;
    }

    public function setClient(?user $client): static
    {
        $this->client = $client;

        return $this;
    }
}
