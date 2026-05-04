<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/solutions', name: 'app_solutions')]
    public function solutions(): Response {
        return $this->render('page/solutions.html.twig');
    }// pour afficher la page solutions.html.twig quand l'user va sur /solutions    

    #[Route('/prix', name: 'app_pricing')]
    public function pricing(): Response {
        return $this->render('page/pricing.html.twig');
    }// pour afficher la page pricing.html.twig quand l'user va sur /prix

    #[Route('/ressources', name: 'app_resources')]
    public function resources(): Response {
        return $this->render('page/resources.html.twig');
    }// pour afficher la page resources.html.twig quand l'user va sur /ressources
}