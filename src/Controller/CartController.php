<?php

namespace App\Controller;

use App\Repository\ProductRepository;// appeler  le repository des produits pour faire des requetes en BDD sur les produits
use Stripe\Checkout\Session;// pour utiliser la classe Session de Stripe, qui permet de créer des sessions de paiement pour le processus de checkout. Cela nous permettra d'intégrer Stripe dans notre application pour gérer les paiements en ligne.
use Stripe\Stripe;// pour utiliser la classe Stripe, qui est la classe principale de la bibliothèque Stripe. Elle nous permet de configurer la clé API et d'accéder à toutes les fonctionnalités de Stripe, comme la création de sessions de paiement, la gestion des clients, etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;//pour dire que cette classe est un controller symfony
use Symfony\Component\HttpFoundation\RequestStack;// pour utiliser la classe RequestStack, qui nous permet d'accéder à la session de l'utilisateur. Cela est nécessaire pour gérer le panier (panier) de l'utilisateur, qui est stocké dans la session.
use Symfony\Component\HttpFoundation\Response;//pour retourner des reponses http à l'user
use Symfony\Component\Routing\Attribute\Route; // pour définir les routes de notre application, ce qui nous permet de lier des URL spécifiques à des méthodes de notre controller. Cela nous permet de gérer les différentes actions liées au panier et au paiement.
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;// pour générer des URL à partir des noms de routes, ce qui nous permet de créer des liens vers les pages de succès et d'annulation du paiement dans Stripe. Cela nous permet d'assurer une navigation fluide pour l'utilisateur après le processus de paiement, en le redirigeant vers les pages appropriées en fonction du résultat du paiement (succès ou annulation).         

class CartController extends AbstractController// pour dire que ce controller hérite de la classe AbstractController, ce qui lui permet d'utiliser les fonctionnalités de base des controllers Symfony, comme le rendu de templates, la gestion des redirections, l'ajout de messages flash, etc.
{
    #[Route('/cart', name: 'app_cart')]
    public function index(RequestStack $requestStack, ProductRepository $productRepo): Response
    {
        $session = $requestStack->getSession(); //pour accéeder ou recupérer la session de l'utilisateur
        $panier = $session->get('panier', []);// pour récupérer le panier de l'utilisateur à partir de la session, en utilisant la clé 'panier'. Si le panier n'existe pas encore dans la session, on retourne un tableau vide par défaut. Le panier est généralement un tableau associatif où les clés sont les identifiants des produits et les valeurs sont les quantités correspondantes.
        $panierData = [];//là on itnitialise les données du panier et ces données se rempliront dans la boucle foreach suivante 
        $total = 0;// pour initialiser le total du panier à 0, et ce total sera calculé dans la boucle foreach suivante en multipliant le prix de chaque produit par sa quantité et en ajoutant la TVA

        foreach ($panier as $id => $quantity) {//pour parcourrir chaque produit su panier et la quantity de ce produit se trouvant dans le panier 
            $product = $productRepo->find($id); //on retrouve le produit en fonction de son id grâce au repository des produits, et on le met dans $product. Cela nous permet d'accéder aux informations du produit, comme son nom, son prix, etc., pour les afficher dans le panier et calculer le total.
            if ($product) {// si c'est un produit valide ,il existe en base de données
                $panierData[] = ['product' => $product, 'quantity' => $quantity];//alors les données du panier seront le produit et sa quantité, et on les ajoute au tableau $panierData qui contient toutes les informations nécessaires pour afficher le panier à l'utilisateur. Chaque élément de $panierData est un tableau associatif avec une clé 'product' qui contient l'objet du produit et une clé 'quantity' qui contient la quantité de ce produit dans le panier.
                $total += $product->getPrixHt() * (1 + $product->getVatRate()) * $quantity; //pour calculer le totl du panier qui va s'augmenter à chaque itération de la boucle en multipliant le prix HT du produit par (1 + taux de TVA) pour obtenir le prix TTC, puis en multipliant par la quantité de ce produit dans le panier, et enfin en ajoutant ce montant au total global du panier. Cela nous permet d'obtenir le montant total que l'utilisateur devra payer pour les produits dans son panier, en incluant la TVA.
            }
        }
        return $this->render('cart/index.html.twig', ['items' => $panierData, 'total' => $total]);// pour rendre la vue Twig 'cart/index.html.twig' et lui passer les données du panier à afficher. On utilise la clé 'items' pour accéder aux données du panier dans le template, et la clé 'total' pour accéder au montant total du panier. Cela nous permettra d'afficher les produits, leurs quantités, leurs prix, et le total du panier dans la page du panier.
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, RequestStack $requestStack, ProductRepository $productRepo): Response
    {
        $session = $requestStack->getSession();//on recupère la session de l'user
        $panier = $session->get('panier', []);//on recup le panier de l'user
        
        // Optionnel : Vérifier le stock dès l'ajout au panier
        $product = $productRepo->find($id);
        $currentQtyInCart = $panier[$id] ?? 0; //donne moi la quantitédu produit dans le panier si elle existe sinon return moi 0

        if ($product && $product->getQuantity() <= $currentQtyInCart) { //inferieur ou egal si la quantité du produit stocké en database est inferieur a celle du produit ajoute dans le panier 
            $this->addFlash('danger', 'Plus de stock disponible pour ce produit !');
            return $this->redirectToRoute('app_home');
        }

        $panier[$id] = $currentQtyInCart + 1;// on modifie la quantite de ce produit dans le panier 
        $session->set('panier', $panier);//on modifie le panier de cette session 
        $this->addFlash('success', 'Produit ajouté !');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/cart/payment', name: 'cart_payment')]
    public function payment(RequestStack $requestStack, ProductRepository $productRepo): Response
    {
        $session = $requestStack->getSession();//session de l'user
        $panier = $session->get('panier', []);//le panier de l'user a partir de la session
        
        if (empty($panier)) {//empty est une fonctiuon php pour vérifier si la liste (le panier ) es vide (true)
            return $this->redirectToRoute('app_cart'); //donc ici si le panier est vide on redirige l'user à la page 
        }

        // --- SÉCURITÉ STOCK AVANT PAIEMENT ---
        foreach ($panier as $id => $quantity) {
            $product = $productRepo->find($id);
            if (!$product || $product->getQuantity() < $quantity) {
                $this->addFlash('danger', 'Désolé, le stock de ' . ($product ? $product->getName() : 'produit inconnu') . ' est épuisé ou insuffisant.'); //au milieu dans la () on a une condition ternaire condition?si vrai:si faux
                return $this->redirectToRoute('app_cart');
            }
        }
        // ------------------------------------- partie importante

        // On initialise Stripe avec la clé secrète du .env
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY'] ?? $_SERVER['STRIPE_SECRET_KEY']);// on donne à l'api stripe la clé secrete si elle n'est pas dans $_ENV on la cherche  dans $_ENV

        $lineItems = [];//on crée une liste vide pour envoyer à stripe la liste des articles
        foreach ($panier as $id => $quantity) {
            $product = $productRepo->find($id);
            if ($product) {
                $lineItems[] = [
                    'price_data' => [//on donne les infos de prix de cet article 
                        'currency' => 'eur',//pour dire à stripe que le payement se fera en euro
                        'product_data' => ['name' => $product->getName()],
                        'unit_amount' => (int)(($product->getPrixHt() * (1 + $product->getVatRate())) * 100),// on convertit les prix en centimes et on enlève les décimales pour les laisser en int 
                    ],
                    'quantity' => $quantity,//pour envoyer la quantity de ce produit dans le panier à stripe
                ];
            }
        }

        $checkoutSession = Session::create([ //Et là on demande à stripe de créer une page  de payement avec tous les parametres qu'il faut
            'payment_method_types' => ['card'],// pour dire à stripe que le moyen de paiement accepté est la carte bancaire
            'line_items' => $lineItems,// pour envoyer à stripe la liste des articles à payer, avec leurs prix et leurs quantités
            'mode' => 'payment',// pour indiquer que le mode de paiement est un paiement unique
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),// pour dire à stripe que si le paiement est réussi, il doit rediriger l'utilisateur vers la page de succès du paiement, en générant l'URL de cette page à partir du nom de la route 'payment_success'. L'option UrlGeneratorInterface::ABSOLUTE_URL permet de générer une URL absolue complète, ce qui est nécessaire pour les redirections depuis Stripe.
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),// pour dire à stripe que si le paiement est annulé, il doit rediriger l'utilisateur vers la page d'annulation du paiement, en générant l'URL de cette page à partir du nom de la route 'payment_cancel'.
        ]);

        return $this->redirect($checkoutSession->url, 303);// pour rediriger l'utilisateur vers la page de paiement de Stripe, en utilisant l'URL fournie par la session de checkout créée. Le code de statut 303 indique une redirection après une requête POST, ce qui est approprié dans ce cas pour rediriger l'utilisateur vers une page externe (Stripe) après avoir initié le processus de paiement.
    }
}