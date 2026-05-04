<?php

namespace App\Entity;  /** c'est le chemin vers ce fichier le code postal de tous les fichiers  */
/**c'est la liste de tous les imports qu'on va utiliser dans ce fichier  */
use App\Repository\UserRepository;  /** pour chercher des users dans ma base de données  */
use Doctrine\Common\Collections\ArrayCollection; /** pour gérer une liste d'objets (les ventes) */
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;/** Pour dire à Doctrine comment mapper en BDD */
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;/**pour obliger la classe à gérer des  mots de passe  */
use Symfony\Component\Security\Core\User\UserInterface;/**Oblige la classe à avoir certaines méthodes Symfony  inerface utilisateur  */

#[ORM\Entity(repositoryClass: UserRepository::class)]/** pour Dire à Doctrine "cette classe est une table en BDD". Chaque propriété deviendra une colonne. */
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]/** là c'est une contrainte d'unicité pour dire à la BDD que deux clients ne peuvent pas avoir le meme email  */
class User implements UserInterface, PasswordAuthenticatedUserInterface  /** la classe s'engage  à respecter les règles de symfony pour les utilisateurs ,si une méthode obligatoire manque, Symfony plante. */
{
    #[ORM\Id]  /** la clé primaire de la table user  */
    #[ORM\GeneratedValue]
    #[ORM\Column]/** les colonnes  */
    private ?int $id = null;  /** pour dire que cette clé peut être nul au départ avant d'être suavegarder en BDD quoi  */

    #[ORM\Column(length: 180)]
    private ?string $email = null;  /**nul aussi au départ  */

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = []; /** le tableau pour les rôles des utilisateurs stocké en JSON en BDD (['role admin']) par defaut c'est un tableau vide :[] */

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;  /**les mots de passe hashés des utilisateur(jamais en clair) doctrine les stockent tels qu'ils sont en BDD */

    /**
     * @var Collection<int, Sale>  les ventes réalisées  avec une relation OneToMany (un client peut avoir plusieurs ventes)
     */
    #[ORM\OneToMany(targetEntity: Sale::class, mappedBy: 'client')]/** pour dire que dans l'entité sale la proprieté qui fait le lien est client parceque dans sale y'a une colonne client_id qu'on pourra utiliser pour les liaisons join inner join */
    private Collection $sales; /**un client peut avoir un ou plusieurs venntes (sales) parcequ'on utulise onetomany ici  */
      /** Le constructeur */
    public function __construct()
    {
        $this->sales = new ArrayCollection();  /** Quand on crée un nouvel utilisateur, Symfony initialise automatiquement $sales comme une liste vide */
    }/**Sans ça, la liste serait null et planterait dès qu'on essaie d'y ajouter une vente. */
/**gettters setters */
    public function getId(): ?int
    {
        return $this->id; /**pour retourner l'id de l'utulisateur qui est généré automatiquement par la base de données et qu'on pourra jamais modifier mannuellement (donc ici on peut pas utuliser setId) par getId qui permet de lire des proprietés privées  */
    }

    public function getEmail(): ?string  /** pour lire l'email de l'utulisateur */
    {
        return $this->email;
    }

    public function setEmail(string $email): static /**pour modifier l'email du user  */
    {
        $this->email = $email; /**écrase l'ancienne version par la nouvelle  */

        return $this;   /**permet de chaîner les méthodes : pour donner des roles (admin...) */
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email; /**C'est une méthode obligatoire de UserInterface, Symfony l'utilise pour identifier l'utilisateur connecté. Ici c'est l'email qui sert d'identifiant unique. */
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // au moins un role pour tt utilisateur par defaut si on remplit pas le tableau des roles, on veut que tt utilisateur ait au moins ROLE_USER
        $roles[] = 'ROLE_USER'; /** le role_user rejoins tous les roles qui sont deja  en BDD  */

        return array_unique($roles); /**Retourne les rôles de l'utilisateur. La ligne $roles[] = 'ROLE_USER' garantit que tout utilisateur a au moins ROLE_USER, même si on n'en a pas défini array_unique évite les doublons. */
    }

    /**
     * @param list<string> $roles  /**juste un commentaire pour dire que ce parametre est une liste de textes 
     */
    public function setRoles(array $roles): static /**setRoles pour dire que cette fois ci c'est pas nul  */
    {
        $this->roles = $roles; /**ecrase les anciens roles par les nouveaux  */

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string /** pour lire les mots de passes hashés des users  */   /**les mots de passes sont jamais stockés en vrai en BDD */
    {
        return $this->password; /** retourner le MDP */
    }

    public function setPassword(string $password): static /** modifie le MDP  */
    {
        $this->password = $password;/** ecrase l'ancien mot de passe par le nouveau MDP  */

        return $this;/**  toujours pour le chainage */ 
    }

    /**
     * la serialisation est le fait de transformer un objet en données stockables.
     */
    public function __serialize(): array
    {
        $data = (array) $this; /** tranforme l'objet user entier en tableau php donc toutes les propriétés ($id, $email, $password, $roles...) deviennent des clés du tableau stockées comme données en BDD */
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);   /** pour hasher le mot de passe avant de le stocker en BDD, hash('crc32c', $this->password(le MDP hashé de bycript)) utilise l'algorithme crc32c pour créer un hash du mot de passe. Cela rend le mot de passe illisible et plus sécurisé en cas de fuite de données. */
/** $data["\0".self::class."\0password"] je dois plus comprendre et retenir  cette ligne :::En PHP, quand tu transformes un objet en tableau avec `(array)`, les propriétés **privées** ont automatiquement ce format de clé :
*`` \0 + NomDeLaClasse ou chemin  + \0 + nomDeLaPropriété  */    /** et c'est ca qui donne "\0" . "App\Entity\User" . "\0" . "password"*/
/**Et self::class c'est juste le raccourci PHP pour dire "App\Entity\User" automatiquement. */
        return $data;
    }
/**resume global : Dans le tableau $data, trouve la case qui contient le mot de passe, et remplace le vrai hash bcrypt par un mini hash crc32c pour proteger la session schant que en base de donnee il ne peut y avoir que le vrai hash de bycript  */


/**eraseCredentials() était utilisée pour effacer les données sensibles après connexion. Elle est maintenant dépréciée car Symfony 7 gère ça directement via __serialize(). On la garde vide pour la compatibilité mais elle disparaîtra en Symfony 8. */

#[\Deprecated]
    public function eraseCredentials(): void
    {
            /** Si tu stockais des données sensibles en clair (comme un mot de passe temporaire), tu les effacerais ici. Mais comme on ne stocke que le hash, il n'y a rien à faire du coup on laisse cette partie vide  */
    }

    /**
     * @return Collection<int, Sale> pour dire que cette méthode retourne une collection d'objets de type Sale
     */
    public function getSales(): Collection /**pour lire les ventes et : Collection c'est le type de retour — cette méthode retourne forcément une Collection (jamais un tableau simple, jamais null).
 */
    {
        return $this->sales;  /**retourne la liste de ventes de cet utulisateur  */
    }

    public function addSale(Sale $sale): static 
    {
        if (!$this->sales->contains($sale)) { /**contains() vérifie si cette vente est déjà dans la liste. Le ! inverse la condition donc on dit : "si cette vente N'EST PAS déjà dans la liste, alors...". C'est une protection contre les doublons */
            $this->sales->add($sale); /**On ajoute la vente à la collection de cet utilisateur */
            $sale->setClient($this); /**et là on dit à la vente elle même que c'est cet user son client  */  /**des deux sens quoi  */
        }

        return $this; /** retourne l'objet user pour le chainage  */
    }

    public function removeSale(Sale $sale): static  /** on supprime ou retire  la vente de la  collection  et removeelement fait deux choses en meme temps il supprime l'elementde la liste et retourn true (si l'element etait dans la liste et false (sinon)) */
    {
        if ($this->sales->removeElement($sale)) { /**si la vente de cet user etait bien dans la liste et qu'on la supp alors... */
            
            if ($sale->getClient() === $this) {  /**ici on verifie le client pour cette vente est bien l'utilisateur lui meme Parce qu'entre le moment où on a récupéré la vente et maintenant, quelqu'un aurait pu modifier le client de cette vente. C'est une vérification de sécurité pour ne pas écraser un changement récent */
                $sale->setClient(null); /**et là on dit à la vente que son client est nul parce qu'on vient de supprimer cette vente de la liste de ce client, donc pour éviter les incohérences, on dit à la vente "tu n'as plus de client" */
            }/**En résumé, removeSale() supprime la vente de la liste de l'utilisateur et, si cette vente était bien associée à cet utilisateur, elle dissocie aussi la vente de l'utilisateur en mettant son client à null. Cela garantit que les données restent cohérentes des deux côtés de la relation. */
        }/** Sans cette ligne, la vente continuerait de pointer vers ce User même après la suppression. */

        return $this; /**Même principe que addSale() — retourne le User pour pouvoir chaîner */
    }
}
