<?php

namespace App\Controller;

use App\Entity\Objet;
use App\Form\ObjetType;
use App\Repository\CategorieRepository;
use App\Repository\ObjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/objets')]
class ObjetController extends AbstractController
{
    #[Route('', name: 'objet_index', methods: ['GET'])]
    public function index(ObjetRepository $objetRepository): Response
    {
        return $this->render('objet/index.html.twig', [
            'objets' => $objetRepository->findAll(),
        ]);
    }

    #[Route('/nouvel', name: 'objet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, CategorieRepository $categorieRepository): Response
    {
        $objet = new Objet();
        $form = $this->createForm(ObjetType::class, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($objet);
            $em->flush();

            $this->addFlash('success', 'Objet créé avec succès.');

            return $this->redirectToRoute('objet_index');
        }

        return $this->render('objet/new.html.twig', [
            'objet' => $objet,
            'form' => $form,
        ]);
    }

    #[Route('/{idObjet}', name: 'objet_show', methods: ['GET'])]
    public function show(int $idObjet, ObjetRepository $objetRepository): Response
    {
        $objet = $objetRepository->find($idObjet);

        if (!$objet) {
            throw $this->createNotFoundException('Objet introuvable');
        }

        return $this->render('objet/show.html.twig', [
            'objet' => $objet,
        ]);
    }

    #[Route('/{idObjet}/modifier', name: 'objet_edit', methods: ['GET', 'POST'])]
    public function edit(int $idObjet, Request $request, ObjetRepository $objetRepository, EntityManagerInterface $em): Response
    {
        $objet = $objetRepository->find($idObjet);

        if (!$objet) {
            throw $this->createNotFoundException('Objet introuvable');
        }

        $form = $this->createForm(ObjetType::class, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Objet mis à jour.');

            return $this->redirectToRoute('objet_show', ['idObjet' => $objet->getIdObjet()]);
        }

        return $this->render('objet/edit.html.twig', [
            'objet' => $objet,
            'form' => $form,
        ]);
    }

    #[Route('/{idObjet}/supprimer', name: 'objet_delete', methods: ['POST'])]
    public function delete(int $idObjet, Request $request, ObjetRepository $objetRepository, EntityManagerInterface $em): Response
    {
        $objet = $objetRepository->find($idObjet);

        if (!$objet) {
            throw $this->createNotFoundException('Objet introuvable');
        }

        if ($this->isCsrfTokenValid('delete_objet_'.$objet->getIdObjet(), $request->request->get('_token'))) {
            $em->remove($objet);
            $em->flush();
            $this->addFlash('success', 'Objet supprimé.');
        }

        return $this->redirectToRoute('objet_index');
    }
}

