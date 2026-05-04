<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(ProductRepository $productRepository): Response
    {
        
        return $this->render('shop/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/boutique/produit/{id}', name: 'app_boutique_show')]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');  //vu que ici on cherche à afficher une source ou une page qui n'existe pas du coup on utilise le createNotFoundException pour afficher une page erreur 
        }

        return $this->render('shop/show.html.twig', [
            'product' => $product,
        ]);
    }
}// cette page cherche à afficher une page produit depuis son id apres avoir lister tous les produits 
