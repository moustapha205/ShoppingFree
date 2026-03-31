<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(RequestStack $requestStack, ProductRepository $productRepo): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);
        $panierData = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $product = $productRepo->find($id);
            if ($product) {
                $panierData[] = ['product' => $product, 'quantity' => $quantity];
                $total += $product->getPrixHt() * (1 + $product->getVatRate()) * $quantity;
            }
        }
        return $this->render('cart/index.html.twig', ['items' => $panierData, 'total' => $total]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, RequestStack $requestStack, ProductRepository $productRepo): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);
        
        // Optionnel : Vérifier le stock dès l'ajout au panier
        $product = $productRepo->find($id);
        $currentQtyInCart = $panier[$id] ?? 0;

        if ($product && $product->getQuantity() <= $currentQtyInCart) {
            $this->addFlash('danger', 'Plus de stock disponible pour ce produit !');
            return $this->redirectToRoute('app_home');
        }

        $panier[$id] = $currentQtyInCart + 1;
        $session->set('panier', $panier);
        $this->addFlash('success', 'Produit ajouté !');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/cart/payment', name: 'cart_payment')]
    public function payment(RequestStack $requestStack, ProductRepository $productRepo): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier', []);
        
        if (empty($panier)) {
            return $this->redirectToRoute('app_cart');
        }

        // --- SÉCURITÉ STOCK AVANT PAIEMENT ---
        foreach ($panier as $id => $quantity) {
            $product = $productRepo->find($id);
            if (!$product || $product->getQuantity() < $quantity) {
                $this->addFlash('danger', 'Désolé, le stock de ' . ($product ? $product->getName() : 'produit inconnu') . ' est épuisé ou insuffisant.');
                return $this->redirectToRoute('app_cart');
            }
        }
        // -------------------------------------

        // On initialise Stripe avec la clé secrète du .env
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $lineItems = [];
        foreach ($panier as $id => $quantity) {
            $product = $productRepo->find($id);
            if ($product) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => $product->getName()],
                        'unit_amount' => (int)(($product->getPrixHt() * (1 + $product->getVatRate())) * 100),
                    ],
                    'quantity' => $quantity,
                ];
            }
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkoutSession->url, 303);
    }
}