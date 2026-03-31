<?php

namespace App\Controller;

use App\Entity\AutoEcole;
use App\Form\AutoEcoleType;
use App\Repository\AutoEcoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auto/ecole')]
final class AutoEcoleController extends AbstractController
{
    #[Route(name: 'app_auto_ecole_index', methods: ['GET'])]
    public function index(AutoEcoleRepository $autoEcoleRepository): Response
    {
        return $this->render('auto_ecole/index.html.twig', [
            'auto_ecoles' => $autoEcoleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_auto_ecole_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $autoEcole = new AutoEcole();
        $form = $this->createForm(AutoEcoleType::class, $autoEcole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($autoEcole);
            $entityManager->flush();

            return $this->redirectToRoute('app_auto_ecole_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('auto_ecole/new.html.twig', [
            'auto_ecole' => $autoEcole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_auto_ecole_show', methods: ['GET'])]
    public function show(AutoEcole $autoEcole): Response
    {
        return $this->render('auto_ecole/show.html.twig', [
            'auto_ecole' => $autoEcole,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_auto_ecole_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AutoEcole $autoEcole, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AutoEcoleType::class, $autoEcole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_auto_ecole_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('auto_ecole/edit.html.twig', [
            'auto_ecole' => $autoEcole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_auto_ecole_delete', methods: ['POST'])]
    public function delete(Request $request, AutoEcole $autoEcole, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$autoEcole->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($autoEcole);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_auto_ecole_index', [], Response::HTTP_SEE_OTHER);
    }
}
