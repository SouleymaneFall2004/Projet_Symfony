<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\DTO\ArticleDTO;
use App\Entity\Article;
use Doctrine\ORM\Mapping\Entity;

class ArticleController extends AbstractController
{

    #[Route('/article', name: 'app1_article')]
    public function index(): Response
    {
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/article/index.html');
        // Retourner la vue HTML
        return new Response($htmlContent);
    }

    #[Route('/article/form', name: 'app_article_form')]
    public function form(): Response
    {
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/article/form.html');
        return new Response($htmlContent);
    }

    #[Route('/article/add', name: 'app_article_add')]
    public function add(ArticleRepository $articleRepository, EntityManagerInterface $em, Request $request): Response
    {

        $data = $request->request->all();
        $article = new Article();
        $article->setLibelle($data['libelle']);
        $article->setQteStock($data['qteStock']);
        $article->setPrix($data['prix']);
        $em->persist($article);
        $em->flush();
        return $this->redirectToRoute('app1_article');
    }




    #[Route('/api/article', name: 'api_article')]
    public function apiArticle(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        // Récupérer la page courante depuis la requête (par défaut, page 1)
        $page = $request->query->getInt('page', 1);
        $limit = 5;  // Nombre d'articles par page

        // Créer une requête pour récupérer tous les articles
        $queryBuilder = $articleRepository->createQueryBuilder('a');

        // Utiliser KNP Paginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,   // La requête
            $page,           // La page actuelle
            $limit           // Nombre d'articles par page
        );

        // Extraire les articles et les informations de pagination
        $articles = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $totalPages = $pagination->count();

        // Préparer les données à retourner
        $data = [
            'articles' => array_map(function ($article) {
                return [
                    'id' => $article->getId(),
                    'libelle' => $article->getLibelle(),
                    'prix' => $article->getPrix(),
                    'qteStock' => $article->getQteStock(),
                ];
            }, $articles),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
            ]
        ];

        // Retourner les données sous forme de JSON
        return $this->json($data);
    }


    #[Route('/api/article/{id}', name: 'get_article', methods: ['GET'])]
    public function getArticleById(int $id, EntityManagerInterface $entityManager, ArticleRepository $articleRepository): JsonResponse
    {
        // Récupérer l'article depuis la base de données par son ID
        $article = $articleRepository->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            return new JsonResponse(['error' => 'Article non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Retourner les données de l'article sous forme de JSON
        return new JsonResponse([
            'id' => $article->getId(),
            'nom' => $article->getLibelle(),
            'qteStock' => $article->getQteStock(),
            'prix' => $article->getPrix(),
            // Ajoutez d'autres champs nécessaires ici
        ]);
    }



    /*************  ✨ Codeium Command ⭐  *************/
    /**
     * Store a new article.
     *
     * @Route("/article/store", name="article.store", methods={"POST", "GET"})
     */
    /******  e0dce5be-09f6-4088-a085-a286b76c95f9  *******/
    #[Route('/article/store', name: 'article.store', methods: ['POST', 'GET'])]


    public function store(): Response
    {
        return $this->render('article/form.html.twig', []);
    }







    // #[Route('/article/search', name: 'article.search', methods: ['GET'])]
    // public function search(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    // {

    //     $searchTerm = (bool)  $request->query->get('filter', '');

    //     $searchQuery = $request->query->get('search', '');
    //     $articleDto = new ArticleDTO($searchQuery);
    //     $search = $articleDto->getSearch();

    //     if (empty($search)) {

    //         $articles = $articleRepository->findByDisponible($searchTerm);
    //     } else {
    //         $searchTerm = (bool)  $request->query->get('filter', '');

    //         $articles = $articleRepository->findByanything($search, $searchTerm);
    //     }

    //     $pagination = $paginator->paginate(
    //         $articles,
    //         $request->query->getInt('page', 1),
    //         4
    //     );
    //     return $this->render('article/index.html.twig', [
    //         'pagination' => $pagination,
    //         'search' => $searchTerm


    //     ]);
    // }


    // #[Route('/article/search', name: 'article.search', methods: ['GET'])]
    // public function search(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    // {
    //     $searchTerm = $request->query->get('filter', '');
    //     $search = $request->query->get('search', '');
    //     $articleDto = new ArticleDTO($search);
    //     $searchTerm = $articleDto->getSearch();

    //     if (!empty($searchTerm)) {
    //         $articles = $articleRepository->findByLibelle($searchTerm);
    //     } else {
    //         $articles = $articleRepository->findByDisponible($searchTerm);
    //     }

    //     $pagination = $paginator->paginate(
    //         $articles,
    //         $request->query->getInt('page', 1),
    //         4
    //     );

    //     return $this->render('article/index.html.twig', [
    //         'pagination' => $pagination,
    //         'search' => $searchTerm
    //     ]);
    // }
}
