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
        return $this->render('account/orders.html.twig', [//on demande à twig d'afficher le template des commande d'un user connecté.
            'orders' => $this->getUser()->getSales(), // On récupère les ventes du user via la relation onetoMany entre User et Sale, et on les passe à la vue pour les afficher. getSales() est une méthode générée automatiquement par Doctrine qui permet de récupérer toutes les ventes associées à l'utilisateur connecté. En passant ces ventes à la vue, on peut ensuite les afficher dans le template Twig pour que l'utilisateur puisse voir l'historique de ses commandes.
        ]);
    }
}// c'est la page pour toutes les commandes d'un utilisateur connecté.