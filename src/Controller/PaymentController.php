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
    #[Route('/payment/success', name: 'payment_success')]
    #[IsGranted('ROLE_USER')]
    public function success(RequestStack $requestStack, ProductRepository $productRepo, EntityManagerInterface $em): Response 
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);
        $user = $this->getUser(); 

        if (empty($panier)) {
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
        $session->remove('panier');

        $this->addFlash('success', 'Paiement réussi ! Retrouvez vos achats dans "Mes Commandes".');
        return $this->redirectToRoute('app_home');
    }
    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
    $this->addFlash('danger', 'Le paiement a été annulé. Vous pouvez modifier votre panier et réessayer.');
    return $this->redirectToRoute('app_cart'); // On le redirige vers le panier
    }
}