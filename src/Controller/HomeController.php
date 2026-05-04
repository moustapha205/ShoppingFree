<?php

namespace App\Controller; // le chemin du controller, qui correspond au dossier dans lequel il se trouve. Cela permet à Symfony de trouver et de charger cette classe correctement.

use App\Entity\Sale;// pour appeler l'entité Sale et pouvoir créer des objets de type Sale et les enregistrer en BDD
use App\Repository\ProductRepository;// pour appeler le repository des produits et pouvoir faire des requetes en BDD pour les produits
use App\Service\StockManager;// pour appeler le service StockManager et pouvoir utiliser ses méthodes pour gérer le stock des produits et calculer les prix TTC
use Doctrine\ORM\EntityManagerInterface;// pour appeler l'interface EntityManagerInterface de Doctrine et pouvoir utiliser ses méthodes pour gérer les entités et les transactions en BDD, comme persist() pour enregistrer une entité et flush() pour valider les changements en BDD
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;// pour dire que ce controller hérite de la classe AbstractController, ce qui lui permet d'utiliser les fonctionnalités de base des controllers Symfony, comme le rendu de templates, la gestion des redirections, l'ajout de messages flash, etc.
use Symfony\Component\HttpFoundation\Response;// pour appeler la classe Response de Symfony et pouvoir retourner des réponses HTTP à partir des méthodes du controller, comme des pages HTML, des redirections, des JSON, etc.
use Symfony\Component\Routing\Attribute\Route; // pour utiliser l'attribut Route de Symfony et pouvoir définir les routes associées aux méthodes du controller de manière claire et concise, en spécifiant l'URL, le nom de la route, les méthodes HTTP autorisées, etc.

class HomeController extends AbstractController// pour dire que ce controller hérite de la classe AbstractController, ce qui lui permet d'utiliser les fonctionnalités de base des controllers symfony
{
    #[Route('/', name: 'app_home')] // pour définir la route associée à la méthode index() de ce controller, en spécifiant que l'URL de cette route est '/' (la page d'accueil) et que le nom de cette route est 'app_home', ce qui nous permettra de faire des redirections vers cette route en utilisant son nom dans d'autres parties de l'application.
    public function index(ProductRepository $productRepo): Response//attente reponse http
    {
        $products = $productRepo->findAll();//Doctrine va chercher tous les produits en BDD et les met dans $products.
        //En SQL ça donne : SELECT * FROM product

        return $this->render('home/index.html.twig', [
            'products' => $products,// pour envoyer la liste des produits à la vue Twig, en utilisant la clé 'products' pour y accéder dans le template. Cela nous permettra d'afficher les produits sur la page d'accueil  {% for product in products %}
        ]);
    }

    #[Route('/achat/{id}', name: 'app_buy')] //pour definir la route associée à la methode buy() sachant que la route se trouve à l'url /achat/{id} et que le nom de cette route est 'app_buy', ce qui nous permettra de faire des redirections vers cette route en utilisant son nom dans d'autres parties de l'application. Le {id} dans l'URL indique que cette route attend un paramètre dynamique qui correspond à l'identifiant du produit que l'on souhaite acheter, et ce paramètre sera automatiquement injecté dans la méthode buy() grâce à la correspondance entre le nom du paramètre dans l'URL et le nom de l'argument de la méthode.
    public function buy(int $id, ProductRepository $productRepo, EntityManagerInterface $em, StockManager $stockManager): Response //pour attribuer des nouveux noms aux repertoires appelés dans cette page et attendre une reponse http
    {
        $product = $productRepo->find($id); //là on va afficher le produit qu'on veut acheter en cherchant dans la base de données grâce à son id, et on le met dans $product.
        //  En SQL ça donne : SELECT * FROM product WHERE id = $id

        if (!$product || !$stockManager->canSell($product->getQuantity(), 1)) {  //!product pour dire que si le produit n'existe pas alors ... et !$stockManager->canSell($product->getQuantity(), 1) pour dire que si le stock manager nous dit que on ne peut pas vendre ce produit parce que la quantité en stock est insuffisante pour vendre 1 unité alors...
            $this->addFlash('danger', 'Désolé, stock insuffisant !'); //si produit insuffisant alors on ajoute ce message à l'user pour lui dire que le stock est insuffisant et que l'achat ne peut pas être effectué
            return $this->redirectToRoute('app_home');// et on le redirige vers la page d'accueil
        }

        $totalTtc = $stockManager->calculateTTC(// caLcul du prix total TTC de la vente en utilisant le service StockManager
            (float)$product->getPrixHt(), // on convertit le prix HT du produit en float pour pouvoir faire les calculs, car en BDD on a stocké les prix sous forme de chaînes de caractères pour éviter les problèmes d'arrondi liés aux nombres à virgule flottante en PHP, mais pour faire les calculs on doit les convertir en float
            (float)$product->getVatRate());// on convertit le taux de TVA du produit en float pour pouvoir faire les calculs, même raison que pour le prix HT
           //pour l'instant on convertit le prix HT et le taux de TVA en float et les calculs se feront en float dans  le service stockmanager qui va ensuite stocker le resultat du calcul dans totalTtc qui est une chaîne de caractères, mais on pourrait aussi faire les calculs en string en utilisant des fonctions spécifiques pour les nombres décimaux en PHP, comme bcadd() pour l'addition, bcmul() pour la multiplication, etc. Cela permettrait d'éviter les problèmes d'arrondi liés aux nombres à virgule flottante et de garder une précision maximale dans les calculs de prix
        $sale = new Sale(); //on crée un nouveau objet de type Sale pour enregistrer la vente qui va être effectuée
        $sale->setProductSku($product->getSku());// on attribue à la vente le SKU du produit acheté, pour pouvoir identifier facilement quel produit a été vendu
        $sale->setQuantitySold(1); // on attribue à la vente la quantité vendue, qui est de 1 dans ce cas, car on suppose que l'utilisateur achète une seule unité du produit. Si on voulait gérer des achats de plusieurs unités, il faudrait adapter cette partie du code pour prendre en compte la quantité choisie par l'utilisateur.
        $sale->setTotalTtc($totalTtc); // on attribue à la vente le prix total TTC calculé grâce au service StockManager, pour pouvoir enregistrer le montant de la vente et l'afficher dans les historiques de ventes ou les rapports de chiffre d'affaires

        $product->setQuantity($product->getQuantity() - 1);//on met à jour la quantité du produit en stock en soustrayant 1 à la quantité actuelle, pour refléter le fait qu'une unité de ce produit a été vendue. Cela permet de maintenir à jour le stock des produits et d'éviter les ventes de produits qui ne sont plus disponibles.   

        $em->persist($sale);  // pour dire à Doctrine de prendre en compte cette nouvelle vente et de la préparer à être enregistrée en base de données lors du prochain flush(). Cela permet de gérer les transactions de manière efficace et d'assurer l'intégrité des données en regroupant plusieurs opérations d'enregistrement en une seule transaction.
        $em->flush();// pour dire à Doctrine de valider les changements en base de données, c'est-à-dire d'enregistrer la nouvelle vente et de mettre à jour la quantité du produit en stock. Cela permet de s'assurer que toutes les opérations liées à cette vente sont effectuées de manière atomique, ce qui signifie que soit toutes les opérations réussissent, soit aucune n'est appliquée en cas d'erreur, garantissant ainsi la cohérence des données.

        $this->addFlash('success', 'Achat réussi !'); // pour ajouter un message flash de succès à l'utilisateur pour lui indiquer que l'achat a été effectué avec succès. Les messages flash sont des messages temporaires qui sont stockés dans la session et affichés à l'utilisateur lors de la prochaine requête, ce qui permet de fournir des retours d'information sur les actions effectuées, comme les achats, les erreurs, les confirmations, etc.
        return $this->redirectToRoute('app_home');// et on redirige l'utilisateur vers la page d'accueil après l'achat, pour lui permettre de continuer à naviguer sur le site ou à acheter d'autres produits. Cela permet également de rafraîchir la page et d'afficher les changements liés à l'achat, comme la mise à jour du stock ou l'affichage du message de succès
    }
} 


