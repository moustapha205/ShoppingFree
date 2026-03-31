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
    }

    #[Route('/prix', name: 'app_pricing')]
    public function pricing(): Response {
        return $this->render('page/pricing.html.twig');
    }

    #[Route('/ressources', name: 'app_resources')]
    public function resources(): Response {
        return $this->render('page/resources.html.twig');
    }
}