<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PaymentController extends AbstractController
{
    #[Route('/payment/success', name: 'payment_success')] //apres payement reussi via stripe
    #[IsGranted('ROLE_USER')] 
    public function success(RequestStack $requestStack, ProductRepository $productRepo, EntityManagerInterface $em): Response 
    {
        $session = $requestStack->getSession(); //on récupère la session de l'user
        $panier = $session->get('panier', []);//son panier aussi par la session
        $user = $this->getUser(); //on lite les infos de l'utilisateur connecté pour pouvoir l'associer à la vente qui va être créée, et ainsi garder une trace de quel utilisateur a acheté quels produits, ce qui est important pour les historiques de commandes, les profils clients, etc.

        if (empty($panier)) {// si le panier est vide, on affiche un message d'information à l'utilisateur et on le redirige vers la page d'accueil, car il n'y a rien à enregistrer comme vente
            $this->addFlash('info', 'Votre panier est vide.');
            return $this->redirectToRoute('app_home');
        }

        foreach ($panier as $id => $quantity) {
            $product = $productRepo->find($id);
            if ($product) {
                $sale = new Sale();
                $sale->setProductSku($product->getSku());
                $sale->setQuantitySold($quantity);
                $sale->setTotalTtc((string)($product->getPrixHt() * (1 + $product->getVatRate()) * $quantity));
                
                // On lie la vente à l'utilisateur actuel (Relation ManyToOne créée)
                $sale->setClient($user); 

                // Décrémentation du stock
                $product->setQuantity($product->getQuantity() - $quantity);
                
                $em->persist($sale);
            }
        }

        $em->flush();
        $session->remove('panier');//et on supprime le panier de la session pour que l'utilisateur puisse faire de nouveaux achats sans que les anciens restent dans le panier

        return $this->render('payment/success.html.twig');
    }
    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
    $this->addFlash('danger', 'Le paiement a été annulé. Vous pouvez modifier votre panier et réessayer.');
    return $this->redirectToRoute('app_cart'); // On le redirige vers le panier
    }
}