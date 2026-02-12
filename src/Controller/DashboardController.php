<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\MediaRepository;
use App\Repository\ObjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        CategorieRepository $categorieRepository,
        ObjetRepository $objetRepository,
        MediaRepository $mediaRepository
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categories_count' => $categorieRepository->count([]),
            'objets_count' => $objetRepository->count([]),
            'medias_count' => $mediaRepository->count([]),
        ]);
    }
}

