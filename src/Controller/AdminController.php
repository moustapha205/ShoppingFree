<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] // Sécurité totale : Seul l'admin peut entrer dans ce contrôleur
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]  //dashboard pour voir les statistiques sans avoir a naviguer partout.
    public function dashboard(ProductRepository $productRepo, SaleRepository $saleRepo): Response
    {
        // 1. On récupère toutes les ventes pour les statistiques
        $sales = $saleRepo->findAll();
        
        // 2. Calcul du Chiffre d'Affaires (CA) Total
        $totalCA = 0;
        foreach ($sales as $sale) {
            $totalCA += (float) $sale->getTotalTtc();
        }

        // 3. Récupération des produits pour voir l'état des stocks
        $products = $productRepo->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'sales' => $sales,
            'totalCA' => $totalCA,
            'products' => $products,
            'totalProducts' => count($products),
            'totalSalesCount' => count($sales)
        ]);
    }

    #[Route('/reset-stocks', name: 'app_admin_reset_stocks')]
    public function resetStocks(ProductRepository $productRepo, EntityManagerInterface $em): Response
    {
        $products = $productRepo->findAll();
        foreach ($products as $product) {
            $product->setQuantity(100); // Remise à niveau pour tes tests
        }
        $em->flush();

        $this->addFlash('success', 'Tous les stocks ont été réinitialisés à 100 unités.');
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/fill-database', name: 'app_admin_fill')]
    public function fill(ProductRepository $productRepo, EntityManagerInterface $em): Response
    {
        // Vider tous les produits existants
        foreach ($productRepo->findAll() as $p) {
            $em->remove($p);
        }
        $em->flush();

        $catalog = [
            ['name' => 'iPhone 16 Pro',        'sku' => 'APL-IP16P',  'price' => 1199, 'category' => 'Smartphones', 'image' => '/images/products/iphone16pro.png',  'description' => 'Doté du chip A18 Pro et d\'un système de caméra professionnelle 48 MP, l\'iPhone 16 Pro offre des performances exceptionnelles. Écran Super Retina XDR 6,3 pouces ProMotion 120 Hz, design en titane et autonomie améliorée pour une expérience sans compromis.'],
            ['name' => 'iPhone 16',             'sku' => 'APL-IP16',   'price' => 969,  'category' => 'Smartphones', 'image' => '/images/products/iphone16.png',     'description' => 'L\'iPhone 16 embarque le puissant chip A18 et un bouton Action personnalisable. Avec son appareil photo 48 MP et ses modes photo/vidéo avancés, il capture chaque instant avec une précision remarquable, le tout dans un design en aluminium élégant.'],
            ['name' => 'iPhone 17 Pro',         'sku' => 'APL-IP17P',  'price' => 1299, 'category' => 'Smartphones', 'image' => '/images/products/iphone17pro.png',  'description' => 'L\'iPhone 17 Pro repousse les limites avec le chip A19 Pro, un capteur photo principal de 60 MP et un nouvel écran ProMotion 6,3 pouces encore plus lumineux. Le smartphone le plus avancé jamais conçu par Apple.'],
            ['name' => 'MacBook Air M3',        'sku' => 'APL-MBA-M3', 'price' => 1299, 'category' => 'Ordinateurs', 'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80', 'description' => 'Le MacBook Air M3 redéfinit la légèreté et la puissance. Chip Apple M3 avec CPU 8 cœurs et GPU 10 cœurs, écran Liquid Retina 13,6 pouces, jusqu\'à 18h d\'autonomie. Silencieux, ultra-rapide et élégant.'],
            ['name' => 'AirPods Max',           'sku' => 'APL-APMAX',  'price' => 549,  'category' => 'Audio',       'image' => 'https://images.unsplash.com/photo-1613040809024-b4ef7ba99bc3?w=600&q=80', 'description' => 'Les AirPods Max combinent réduction de bruit active haut de gamme et son Spatial Audio immersif. Arceau et coussinets en aluminium et maille tricotée, 20h d\'autonomie et connexion instantanée avec tous vos appareils Apple.'],
            ['name' => 'iPad Pro M4',           'sku' => 'APL-IPP-M4', 'price' => 1049, 'category' => 'Tablettes',   'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80', 'description' => 'L\'iPad Pro M4 est la tablette la plus fine et la plus puissante jamais conçue. Écran Ultra Retina XDR OLED 13 pouces, chip M4, compatible Apple Pencil Pro. Idéal pour les créatifs et les professionnels exigeants.'],
            ['name' => 'Sony PS5 Slim',         'sku' => 'SONY-PS5S',  'price' => 499,  'category' => 'Consoles',    'image' => 'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?w=600&q=80', 'description' => 'La PlayStation 5 Slim offre les mêmes performances next-gen dans un format 30% plus compact. Chargement ultra-rapide SSD, 3D Audio immersif et graphismes 4K jusqu\'à 120 fps pour vivre des aventures inoubliables.'],
            ['name' => 'Xbox Series X',         'sku' => 'MS-XBX',     'price' => 499,  'category' => 'Consoles',    'image' => 'https://images.unsplash.com/photo-1605901309584-818e25960a8f?w=600&q=80', 'description' => 'La Xbox Series X est la console la plus puissante de Microsoft. 4K à 60 fps, Quick Resume multi-jeux, SSD NVMe ultra-rapide et accès à des centaines de titres via le Xbox Game Pass. La puissance sans limite.'],
            ['name' => 'Nintendo Switch OLED',  'sku' => 'NIN-SWO',    'price' => 319,  'category' => 'Consoles',    'image' => 'https://images.unsplash.com/photo-1617096200347-cb04ae810b1d?w=600&q=80', 'description' => 'La Nintendo Switch OLED embarque un vibrant écran OLED 7 pouces pour une expérience portable sublimée. Profitez de vos jeux favoris à la maison ou en déplacement avec 9h d\'autonomie et des Joy-Con polyvalents.'],
            ['name' => 'Samsung S24 Ultra',     'sku' => 'SAM-S24U',   'price' => 1349, 'category' => 'Smartphones', 'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=600&q=80', 'description' => 'Le Galaxy S24 Ultra est le summum de la gamme Samsung. S Pen intégré, caméra 200 MP, zoom optique 10x, écran Dynamic AMOLED 2X 6,8 pouces et Snapdragon 8 Gen 3 pour des performances hors normes au quotidien.'],
            ['name' => 'GoPro Hero 12',         'sku' => 'GOP-H12',    'price' => 399,  'category' => 'Caméras',     'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=600&q=80', 'description' => 'La GoPro HERO 12 capture vos aventures en vidéo 5.3K60 et photo 27 MP avec HyperSmooth 6.0 pour une stabilisation parfaite. Étanche jusqu\'à 10m, robuste et polyvalente pour tous vos exploits sportifs.'],
            ['name' => 'DJI Mini 4 Pro',        'sku' => 'DJI-M4P',    'price' => 799,  'category' => 'Drones',      'image' => 'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=600&q=80', 'description' => 'Le DJI Mini 4 Pro est un drone compact de 249g offrant vidéo 4K/60fps HDR, détection d\'obstacles omnidirectionnelle et 34 minutes d\'autonomie. Idéal pour les créateurs de contenu en quête de qualité cinématographique.'],
        ];

        foreach ($catalog as $item) {
            $product = new Product();
            $product->setName($item['name']);
            $product->setSku($item['sku']);
            $product->setPrixHt($item['price'] / 1.2);
            $product->setVatRate(0.20);
            $product->setQuantity(rand(5, 50));
            $product->setCategory($item['category']);
            $product->setDescription($item['description']);
            $product->setImage($item['image']);
            $em->persist($product);
        }

        $em->flush();

        $this->addFlash('success', 'Boutique réinitialisée avec 12 produits propres !');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/update-descriptions', name: 'app_admin_update_descriptions')]
    public function updateDescriptions(ProductRepository $productRepo, EntityManagerInterface $em): Response
    {
        $data = [
            'APL-IP16P'  => ['name' => 'iPhone 16 Pro',  'category' => 'Smartphones', 'image' => '/images/products/iphone16pro.png', 'description' => 'Doté du chip A18 Pro et d\'un système de caméra professionnelle 48 MP, l\'iPhone 16 Pro offre des performances exceptionnelles. Écran Super Retina XDR 6,3 pouces ProMotion 120 Hz, design en titane et autonomie améliorée pour une expérience sans compromis.'],
            'IPH_16'     => ['name' => 'iPhone 16',       'category' => 'Smartphones', 'image' => '/images/products/iphone16.png',    'description' => 'L\'iPhone 16 embarque le puissant chip A18 et un bouton Action personnalisable. Avec son appareil photo 48 MP et ses modes photo/vidéo avancés, il capture chaque instant avec une précision remarquable, le tout dans un design en aluminium élégant.'],
            'APL-IP17P'  => ['name' => 'iPhone 17 Pro',  'category' => 'Smartphones', 'image' => '/images/products/iphone17pro.png', 'description' => 'L\'iPhone 17 Pro repousse les limites avec le chip A19 Pro, un capteur photo principal de 60 MP et un nouvel écran ProMotion 6,3 pouces encore plus lumineux. Le smartphone le plus avancé jamais conçu par Apple.'],
            'APL-MBA-M3' => ['category' => 'Ordinateurs', 'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80', 'description' => 'Le MacBook Air M3 redéfinit la légèreté et la puissance. Chip Apple M3 avec CPU 8 cœurs et GPU 10 cœurs, écran Liquid Retina 13,6 pouces, jusqu\'à 18h d\'autonomie. Silencieux, ultra-rapide et élégant.'],
            'APL-APMAX'  => ['category' => 'Audio',       'image' => 'https://images.unsplash.com/photo-1613040809024-b4ef7ba99bc3?w=600&q=80', 'description' => 'Les AirPods Max combinent réduction de bruit active haut de gamme et son Spatial Audio immersif. Arceau et coussinets en aluminium et maille tricotée, 20h d\'autonomie et connexion instantanée avec tous vos appareils Apple.'],
            'APL-IPP-M4' => ['category' => 'Tablettes',   'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80', 'description' => 'L\'iPad Pro M4 est la tablette la plus fine et la plus puissante jamais conçue. Écran Ultra Retina XDR OLED 13 pouces, chip M4, compatible Apple Pencil Pro. Idéal pour les créatifs et les professionnels exigeants.'],
            'SONY-PS5S'  => ['category' => 'Consoles',    'image' => 'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?w=600&q=80', 'description' => 'La PlayStation 5 Slim offre les mêmes performances next-gen dans un format 30% plus compact. Chargement ultra-rapide SSD, 3D Audio immersif et graphismes 4K jusqu\'à 120 fps pour vivre des aventures inoubliables.'],
            'MS-XBX'     => ['category' => 'Consoles',    'image' => 'https://images.unsplash.com/photo-1605901309584-818e25960a8f?w=600&q=80', 'description' => 'La Xbox Series X est la console la plus puissante de Microsoft. 4K à 60 fps, Quick Resume multi-jeux, SSD NVMe ultra-rapide et accès à des centaines de titres via le Xbox Game Pass. La puissance sans limite.'],
            'NIN-SWO'    => ['category' => 'Consoles',    'image' => 'https://images.unsplash.com/photo-1617096200347-cb04ae810b1d?w=600&q=80', 'description' => 'La Nintendo Switch OLED embarque un vibrant écran OLED 7 pouces pour une expérience portable sublimée. Profitez de vos jeux favoris à la maison ou en déplacement avec 9h d\'autonomie et des Joy-Con polyvalents.'],
            'SAM-S24U'   => ['category' => 'Smartphones', 'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=600&q=80', 'description' => 'Le Galaxy S24 Ultra est le summum de la gamme Samsung. S Pen intégré, caméra 200 MP, zoom optique 10x, écran Dynamic AMOLED 2X 6,8 pouces et Snapdragon 8 Gen 3 pour des performances hors normes au quotidien.'],
            'GOP-H12'    => ['category' => 'Caméras',     'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=600&q=80', 'description' => 'La GoPro HERO 12 capture vos aventures en vidéo 5.3K60 et photo 27 MP avec HyperSmooth 6.0 pour une stabilisation parfaite. Étanche jusqu\'à 10m, robuste et polyvalente pour tous vos exploits sportifs.'],
            'DJI-M4P'    => ['category' => 'Drones',      'image' => 'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=600&q=80', 'description' => 'Le DJI Mini 4 Pro est un drone compact de 249g offrant vidéo 4K/60fps HDR, détection d\'obstacles omnidirectionnelle et 34 minutes d\'autonomie. Idéal pour les créateurs de contenu en quête de qualité cinématographique.'],
        ];

        $products = $productRepo->findAll();
        $existingSkus = [];
        foreach ($products as $product) {
            $sku = $product->getSku();
            $existingSkus[] = $sku;
            if (isset($data[$sku])) {
                if (isset($data[$sku]['name'])) {
                    $product->setName($data[$sku]['name']);
                }
                $product->setCategory($data[$sku]['category']);
                $product->setDescription($data[$sku]['description']);
                $product->setImage($data[$sku]['image']);
            }
        }

        // Créer les produits manquants (ex: iPhone 17 Pro)
        foreach ($data as $sku => $item) {
            if (!in_array($sku, $existingSkus) && isset($item['name'])) {
                $product = new \App\Entity\Product();
                $product->setSku($sku);
                $product->setName($item['name']);
                $product->setPrixHt(1399 / 1.2);
                $product->setVatRate(0.20);
                $product->setQuantity(rand(5, 30));
                $product->setCategory($item['category']);
                $product->setDescription($item['description']);
                $product->setImage($item['image']);
                $em->persist($product);
            }
        }

        $em->flush();

        $this->addFlash('success', 'Descriptions et catégories mises à jour !');
        return $this->redirectToRoute('app_home');
    }
}