<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccountController extends AbstractController
{
    #[Route('/account/orders', name: 'app_account_orders')]
    #[IsGranted('ROLE_USER')] // Empêche les non-connectés d'entrer
    public function index(): Response
    {
        return $this->render('account/orders.html.twig', [
            'orders' => $this->getUser()->getSales(), // On récupère les ventes du user
        ]);
    }
}// c'est la page pour toutes les commandes d'un utilisateur connecté.