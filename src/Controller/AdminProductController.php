<?php

namespace App\Controller; // on est dans le dossier Controller

use App\Entity\Product; // on importe l'entité Product pour créer/modifier des produits
use App\Form\AdminProductType; // on importe le formulaire qu'on vient de créer
use App\Repository\ProductRepository; // pour récupérer les produits depuis la base de données
use Doctrine\ORM\EntityManagerInterface; // pour sauvegarder, modifier ou supprimer en base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // classe de base des contrôleurs Symfony
use Symfony\Component\HttpFoundation\Request; // pour lire ce que l'utilisateur a envoyé (données du formulaire)
use Symfony\Component\HttpFoundation\Response; // pour retourner une réponse HTTP (page HTML ou redirection)
use Symfony\Component\Routing\Attribute\Route; // pour définir l'URL de chaque méthode
use Symfony\Component\Security\Http\Attribute\IsGranted; // pour protéger ce contrôleur : seul ROLE_ADMIN peut y accéder

// Toutes les routes de ce contrôleur commencent par /admin
// L'attribut IsGranted bloque tout utilisateur qui n'a pas le rôle ROLE_ADMIN
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] // SÉCURITÉ : seul l'administrateur peut accéder à ce contrôleur
class AdminProductController extends AbstractController
{
    // =========================================================
    // ACTION 1 : LISTE — Afficher tous les produits
    // URL : /admin/products
    // =========================================================
    #[Route('/products', name: 'app_admin_products_list')]
    public function adminProductsList(ProductRepository $productRepo): Response
    {
        // On récupère tous les produits de la base de données
        $products = $productRepo->findAll();

        // On envoie la liste à la vue Twig pour l'affichage
        return $this->render('admin/admin_products_list.html.twig', [
            'products' => $products, // la variable 'products' sera dispo dans le template
        ]);
    }

    // =========================================================
    // ACTION 2 : NOUVEAU — Formulaire pour ajouter un produit
    // URL : /admin/products/new  (GET pour afficher, POST pour soumettre)
    // =========================================================
    #[Route('/products/new', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function adminProductNew(Request $request, EntityManagerInterface $em): Response
    {
        // On crée un produit vide — il sera rempli par le formulaire
        $product = new Product();

        // On crée le formulaire en le liant à l'objet $product
        $form = $this->createForm(AdminProductType::class, $product);

        // handleRequest() lit les données soumises (POST) et les injecte dans $product
        $form->handleRequest($request);

        // Si le formulaire a été soumis ET que toutes les validations sont OK
        if ($form->isSubmitted() && $form->isValid()) {
            // On demande à Doctrine de préparer ce nouvel objet pour la sauvegarde
            $em->persist($product);

            // On exécute la requête SQL INSERT en base de données
            $em->flush();

            // On affiche un message de succès (visible sur la prochaine page)
            $this->addFlash('success', 'Produit "' . $product->getName() . '" ajouté avec succès !');

            // On redirige vers la liste des produits admin
            return $this->redirectToRoute('app_admin_products_list');
        }

        // Si on arrive ici c'est qu'on affiche juste le formulaire (GET)
        // ou que le formulaire a été soumis mais contient des erreurs
        return $this->render('admin/admin_product_new.html.twig', [
            'form' => $form, // on passe le formulaire au template Twig
        ]);
    }

    // =========================================================
    // ACTION 3 : MODIFIER — Formulaire pour éditer un produit existant
    // URL : /admin/products/{id}/edit  (GET pour afficher, POST pour soumettre)
    // {id} est l'identifiant du produit à modifier
    // =========================================================
    #[Route('/products/{id}/edit', name: 'app_admin_product_edit', methods: ['GET', 'POST'])]
    public function adminProductEdit(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        // Symfony injecte automatiquement le produit correspondant à l'id dans l'URL.
        // Si aucun produit n'est trouvé avec cet id, Symfony retourne une erreur 404.

        // On crée le formulaire en le liant au produit EXISTANT (les champs seront pré-remplis)
        $form = $this->createForm(AdminProductType::class, $product);

        // handleRequest() lit les données soumises et les met à jour dans $product
        $form->handleRequest($request);

        // Si le formulaire a été soumis ET validé
        if ($form->isSubmitted() && $form->isValid()) {
            // Pas besoin de persist() ici car le produit existe déjà en BDD.
            // Doctrine détecte automatiquement les changements sur un objet déjà géré.
            $em->flush(); // on enregistre les modifications en base de données

            $this->addFlash('success', 'Produit "' . $product->getName() . '" modifié avec succès !');

            // On redirige vers la liste après la modification
            return $this->redirectToRoute('app_admin_products_list');
        }

        // On affiche le formulaire pré-rempli avec les données actuelles du produit
        return $this->render('admin/admin_product_edit.html.twig', [
            'form' => $form, // le formulaire avec les données déjà remplies
            'product' => $product, // le produit en cours de modification (pour l'afficher dans le titre)
        ]);
    }

    // =========================================================
    // ACTION 4 : SUPPRIMER — Supprimer un produit
    // URL : /admin/products/{id}/delete  (POST uniquement pour des raisons de sécurité)
    // On utilise POST (via un formulaire avec un bouton) et non GET
    // car un simple lien GET pourrait être déclenché par accident (ex: robots d'indexation)
    // =========================================================
    #[Route('/products/{id}/delete', name: 'app_admin_product_delete', methods: ['POST'])]
    public function adminProductDelete(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie le token CSRF pour s'assurer que la requête vient bien de NOTRE formulaire
        // et pas d'un site malveillant qui essaierait de supprimer des produits à l'insu de l'admin
        if ($this->isCsrfTokenValid('delete-product-' . $product->getId(), $request->getPayload()->getString('_token'))) {
            // Le token est valide : on peut supprimer
            $em->remove($product); // on marque le produit pour suppression
            $em->flush(); // on exécute le DELETE en base de données

            $this->addFlash('success', 'Produit supprimé avec succès.');
        } else {
            // Si le token n'est pas valide, on refuse et on prévient
            $this->addFlash('danger', 'Action non autorisée : token invalide.');
        }

        // Dans tous les cas on retourne sur la liste
        return $this->redirectToRoute('app_admin_products_list');
    }
}
