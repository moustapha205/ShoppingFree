<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Repository\ProductRepository;
use App\Service\StockManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // Vérifie bien cet import

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepo): Response
    {
        $products = $productRepo->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/achat/{id}', name: 'app_buy')]
    public function buy(int $id, ProductRepository $productRepo, EntityManagerInterface $em, StockManager $stockManager): Response
    {
        $product = $productRepo->find($id);

        if (!$product || !$stockManager->canSell($product->getQuantity(), 1)) {
            $this->addFlash('danger', 'Désolé, stock insuffisant !');
            return $this->redirectToRoute('app_home');
        }

        $totalTtc = $stockManager->calculateTTC((float)$product->getPrixHt(), (float)$product->getVatRate());

        $sale = new Sale();
        $sale->setProductSku($product->getSku());
        $sale->setQuantitySold(1);
        $sale->setTotalTtc($totalTtc);

        $product->setQuantity($product->getQuantity() - 1);

        $em->persist($sale);
        $em->flush();

        $this->addFlash('success', 'Achat réussi !');
        return $this->redirectToRoute('app_home');
    }
}