<?php

namespace App\Controller;

use App\Entity\Brand;// on importe l'entité Brand pour pouvoir l'utiliser dans ce contrôleur. L'entité Brand représente une marque dans le système, et en l'important, on peut créer, afficher, modifier ou supprimer des instances de cette entité dans les différentes méthodes du contrôleur.
use App\Form\BrandType;// on importe la classe de formulaire BrandType pour pouvoir l'utiliser dans ce contrôleur. La classe BrandType est une classe de formulaire personnalisée que vous avez créée pour gérer les champs liés à l'entité Brand
use App\Repository\BrandRepository;//on importe le repertoire des marques pour pouvoir l'utiliser dans ce controller 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;// pour gérer les requêtes HTTP, notamment pour récupérer les données du formulaire soumis par l'utilisateur lors de la création ou de la modification d'une marque.
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/brand')]
final class BrandController extends AbstractController 
{
    #[Route(name: 'app_brand_index', methods: ['GET'])]
    public function index(BrandRepository $brandRepository): Response
    {
        return $this->render('brand/index.html.twig', [
            'brands' => $brandRepository->findAll(),//pour recuperer toutes les marques de la base de données par le findAll
        ]);
    }

    #[Route('/new', name: 'app_brand_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);//pour verifier si le formulaire a été soumis pour traitewr les données envoyées par l'user 

        if ($form->isSubmitted() && $form->isValid()) {//si le formulaire est soumis et valide apres la verification de la securité (tokencsrf correspond toujours )
            $entityManager->persist($brand);//pour dire à doctrine de prendre en compte cette nouvelle entité Brand et de la préparer à être enregistrée en base de données lors du prochain flush(). Cela permet de gérer les transactions de manière efficace et d'assurer l'intégrité des données en regroupant plusieurs opérations d'enregistrement en une seule transaction.
            $entityManager->flush();//pour faire la sauvegarde de la nouvelle marque en base de données en exécutant toutes les opérations d'enregistrement en attente. Cela permet de s'assurer que la nouvelle marque est enregistrée de manière atomique, ce qui signifie que soit toutes les opérations réussissent, soit aucune n'est appliquée en cas d'erreur, garantissant ainsi la cohérence des données.

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);//après la création de la nouvelle marque, on redirige l'utilisateur vers la page d'index des marques pour lui permettre de voir la liste mise à jour des marques, y compris la nouvelle marque qu'il vient de créer. Cela permet également de fournir un retour d'information sur le succès de l'opération de création et d'orienter l'utilisateur vers la suite de son expérience sur le site.
        }

        return $this->render('brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);// si le formulaire n'est pas soumis ou n'est pas valide, on affiche le formulaire de création de marque pour que l'utilisateur puisse le remplir et créer une nouvelle marque. En passant l'instance $brand et le formulaire $form à la vue, on permet à Twig d'afficher les champs du formulaire et de pré-remplir les données de la marque en cas de validation échouée, offrant ainsi une meilleure expérience utilisateur.
    }

    #[Route('/{id}', name: 'app_brand_show', methods: ['GET'])]
    public function show(Brand $brand): Response
    {
        return $this->render('brand/show.html.twig', [// pour afficher les détails d'une marque spécifique. En utilisant l'argument de type Brand dans la méthode show(), Symfony va automatiquement récupérer l'entité Brand correspondante à l'ID fourni dans l'URL et l'injecter dans la méthode. En passant cette entité à la vue, on permet à Twig d'afficher les informations détaillées de la marque, comme son nom, sa description, etc
            'brand' => $brand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_brand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);//pour voir si le formulaire a été soumis pour traiter les données envoyées par l'user lors de la modification d'une marque. En utilisant l'argument de type Brand dans la méthode edit(), Symfony va automatiquement récupérer l'entité Brand correspondante à l'ID fourni dans l'URL et l'injecter dans la méthode, ce qui permet de pré-remplir le formulaire avec les données actuelles de la marque pour que l'utilisateur puisse les modifier facilement.

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();// pour valider les changements en base de données après la modification de la marque. Comme l'entité Brand est déjà gérée par Doctrine (car elle a été récupérée automatiquement grâce à l'argument de type), il suffit d'appeler flush() pour enregistrer les modifications apportées à cette entité en base de données. Cela permet de s'assurer que les changements sont appliqués de manière atomique, garantissant ainsi la cohérence des données.

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);// apres la modification de la marque, on redirige l'utilisateur vers la page d'index des marques pour lui permettre de voir la liste mise à jour des marques, y compris les modifications apportées à la marque qu'il vient de modifier. Cela permet également de fournir un retour d'information sur le succès de l'opération de modification et d'orienter l'utilisateur vers la suite de son expérience sur le site.
        }

        return $this->render('brand/edit.html.twig', [// si le formulaire n'est pas soumis ou n'est pas valide, on affiche le formulaire de modification de marque pour que l'utilisateur puisse le remplir et modifier la marque. En passant l'instance $brand et le formulaire $form à la vue, on permet à Twig d'afficher les champs du formulaire pré-remplis avec les données actuelles de la marque, offrant ainsi une meilleure expérience utilisateur lors de la modification.
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_brand_delete', methods: ['POST'])]// pour que la suppression se fasse via la méthode POST un formulaire quoi
    public function delete(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->getPayload()->getString('_token'))) {// pour vérifier la validité du token CSRF avant de procéder à la suppression de la marque. En utilisant l'argument de type Brand dans la méthode delete(), Symfony va automatiquement récupérer l'entité Brand correspondante à l'ID fourni dans l'URL et l'injecter dans la méthode, ce qui permet de vérifier que le token CSRF est valide pour cette marque spécifique avant de la supprimer. Cela ajoute une couche de sécurité pour éviter les attaques CSRF lors de la suppression d'une marque.
            $entityManager->remove($brand);//on supprime cette marque de la base de données en utilisant la méthode remove() de l'EntityManager
            $entityManager->flush();// pour valider la suppression de la marque en base de données. Comme l'entité Brand est déjà gérée par Doctrine (car elle a été récupérée automatiquement grâce à l'argument de type), il suffit d'appeler flush() après remove() pour enregistrer la suppression de cette entité en base de données. Cela permet de s'assurer que la suppression est appliquée de manière atomique, garantissant ainsi la cohérence des données.
        }

        return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);//redirection de l'admin vers la page d'index des marques après la suppression d'une marque, pour lui permettre de voir la liste mise à jour des marques, sans la marque qu'il vient de supprimer. Cela permet également de fournir un retour d'information sur le succès de l'opération de suppression et d'orienter l'utilisateur vers la suite de son expérience sur le site.
    }
}
