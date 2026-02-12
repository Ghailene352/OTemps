<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories')]
class CategorieController extends AbstractController
{
    #[Route('', name: 'categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/nouvelle', name: 'categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès.');

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{idCategorie}', name: 'categorie_show', methods: ['GET'])]
    public function show(int $idCategorie, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->find($idCategorie);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{idCategorie}/modifier', name: 'categorie_edit', methods: ['GET', 'POST'])]
    public function edit(int $idCategorie, Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        $categorie = $categorieRepository->find($idCategorie);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Catégorie mise à jour.');

            return $this->redirectToRoute('categorie_show', ['idCategorie' => $categorie->getIdCategorie()]);
        }

        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{idCategorie}/supprimer', name: 'categorie_delete', methods: ['POST'])]
    public function delete(int $idCategorie, Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        $categorie = $categorieRepository->find($idCategorie);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        if ($this->isCsrfTokenValid('delete_categorie_'.$categorie->getIdCategorie(), $request->request->get('_token'))) {
            $em->remove($categorie);
            $em->flush();
            $this->addFlash('success', 'Catégorie supprimée.');
        }

        return $this->redirectToRoute('categorie_index');
    }
}

