<?php

namespace App\Controller\Front;

use App\Repository\CategorieRepository;
use App\Repository\ObjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/front', name: 'front_')]
class TableController extends AbstractController
{
    public function __construct(private HttpClientInterface $httpClient) {}

    #[Route('/', name: 'home')]
    public function index(CategorieRepository $categorieRepo): Response
    {
        $categories = $categorieRepo->findAll();
        return $this->render('front/home.html.twig', ['categories' => $categories]);
    }

    #[Route('/categorie/{idCategorie}', name: 'category_show')]
    public function showCategory(int $idCategorie, CategorieRepository $categorieRepo, ObjetRepository $objetRepo): Response
    {
        $categorie = $categorieRepo->find($idCategorie);
        
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        
        $objets = $objetRepo->findBy(['categorie' => $categorie]);
        
        return $this->render('front/table/show.html.twig', [
            'categorie' => $categorie,
            'objets' => $objets,
        ]);
    }

    #[Route('/expert', name: 'expert')]
    public function expert(): Response
    {
        return $this->render('front/expert.html.twig');
    }

#[Route('/expert/analyze', name: 'expert_analyze', methods: ['POST'])]
public function analyzeImage(Request $request): JsonResponse
{
    $uploadedFile = $request->files->get('image');
    if (!$uploadedFile) {
        return $this->json(['error' => 'Aucune image reçue'], 400);
    }

    try {
        $apiKey = $_ENV['GOOGLE_GEMINI_KEY'];
        $imageData = base64_encode(file_get_contents($uploadedFile->getPathname()));
        


$apiKey = trim($_ENV['GOOGLE_GEMINI_KEY']); 

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

$response = $this->httpClient->request('POST', $url, [
    'headers' => ['Content-Type' => 'application/json'],
    'json' => [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Analyse cet objet artisanal de musée : type, matière, époque et valeur.'],
                    [
                        'inlineData' => [
                            'mimeType' => $uploadedFile->getMimeType(),
                            'data' => base64_encode(file_get_contents($uploadedFile->getPathname())),
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
        $result = $response->toArray();
        
        // Sécurité pour éviter l'erreur si l'IA ne répond pas de texte
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? "L'expert n'a pas pu analyser cette image.";

        return $this->json(['success' => true, 'analysis' => $text]);

    } catch (\Exception $e) {
        // Cela te permettra de voir l'erreur réelle dans la console F12 si ça échoue encore
        return $this->json(['error' => 'Erreur API: ' . $e->getMessage()], 500);
    }
}}