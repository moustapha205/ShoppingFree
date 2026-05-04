<?php

namespace App\Entity; // toujours le chemin de l'entité

use App\Repository\ProductRepository; //pour appeler le repository des produits et pouvoir faire des requetes en BDD pour les produits
use Doctrine\DBAL\Types\Types; //pour utiliser les types de données spécifiques de Doctrine, comme DECIMAL et TEXT, qui ne sont pas des types PHP natifs. Cela permet de définir précisément comment les données seront stockées en base de données. Par exemple, Types::DECIMAL indique que la colonne doit être traitée comme un nombre décimal avec une précision et une échelle spécifiques, tandis que Types::TEXT indique que la colonne doit être traitée comme un texte long.
use Doctrine\ORM\Mapping as ORM; //pour dire à doctrine comment mapper en BDD

#[ORM\Entity(repositoryClass: ProductRepository::class)] /** pour dire que cette classe est une entité Doctrine et que son repository associé est ProductRepository, ce qui nous permettra de faire des requêtes spécifiques sur les produits en utilisant ce repository. */
class Product 
{
    #[ORM\Id] // pour dire que c'est la clé primaire de la table en BDD
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;// pour dire que c'est un entier qui peut être null (avant d'être enregistré en BDD) et que c'est une colonne en BDD

    #[ORM\Column(length: 50)]
    private ?string $sku = null;// pour dire que c'est une chaîne de caractères qui peut être null (avant d'être enregistré en BDD) et que c'est une colonne en BDD avec une longueur maximale de 50 caractères

    #[ORM\Column(length: 255)]
    private ?string $name = null; // pour dire que c'est une chaîne de caractères qui peut être null (avant d'être enregistré en BDD) et que c'est une colonne en BDD avec une longueur maximale de 255 caractères

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix_ht = null; // pour dire que c'est une chaîne de caractères qui peut être null (avant d'être enregistré en BDD) et que c'est une colonne en BDD de type DECIMAL avec une précision de 10 chiffres au total et 2 chiffres après la virgule. On utilise une chaîne de caractères pour les prix pour éviter les problèmes d'arrondi liés aux nombres à virgule flottante en PHP.

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2)]
    private ?string $vat_rate = null;  // pour dire que c'est une chaîne de caractères qui peut être null (avant d'être enregistré en BDD) et que c'est une colonne en BDD de type DECIMAL avec une précision de 3 chiffres au total et 2 chiffres après la virgule. Cela permet de stocker des taux de TVA comme 20.00 ou 5.50 de manière précise

    #[ORM\Column]
    private ?int $quantity = null;// pareil

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $category = null; //pareil

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): ?int  //lire l'id du produit, qui peut être null avant d'être enregistré en BDD
    {
        return $this->id;// retourne l'id du produit
    }

    public function getSku(): ?string //lire le SKU du produit, qui peut être null avant d'être enregistré en BDD
    {
        return $this->sku; //retourne le SKU du produit le sku c'est un code unique qu'on donne a chaque produit pour l'identifier des autres produits, c'est comme un code barre ou un reference produit quoi et la diff avec un ID c'est que l'ID est fourni par la base de donnée et est utilisable que par symfony/doctrine alors que le SKU c'est moi même je le choisis 
    }

    public function setSku(string $sku): static // pour modifier le sku d'un caractere qui ne peut pas être null (il doit être fourni) et retourne l'objet lui même pour le chainage
    {
        $this->sku = $sku;

        return $this;
    }

    public function getName(): ?string //lire le nom du produit, qui peut être null avant d'être enregistré en BDD
    {
        return $this->name;
    }

    public function setName(string $name): static // pour modifier le nom d'un caractere qui ne peut pas être null          
    {
        $this->name = $name;

        return $this;
    }

    public function getPrixHt(): ?string
    {
        return $this->prix_ht;
    }

    public function getPriceHt(): ?string
    {
        return $this->prix_ht;
    }

    public function setPrixHt(string $prix_ht): static
    {
        $this->prix_ht = $prix_ht;

        return $this;
    }

    public function getVatRate(): ?string
    {
        return $this->vat_rate;
    }

    public function setVatRate(string $vat_rate): static
    {
        $this->vat_rate = $vat_rate;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }//toute cette partie restante c'est les guetter et setter (lecteur,modificateur)
}
//en résumé cette classe Product est une entité Doctrine qui représente un produit dans notre application.
//  Elle contient des propriétés comme id, sku, name, prix_ht, vat_rate, quantity, category, description et image, 
// chacune avec des annotations qui définissent comment elles sont mappées en base de données.
//  La classe inclut également des méthodes getter et setter pour chaque propriété,
//  permettant de lire et de modifier les valeurs de ces propriétés de manière fluide grâce au retour de $this dans les setters.