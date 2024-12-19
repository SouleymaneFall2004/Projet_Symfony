<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\DTO\ArticleDTO;
use App\Entity\DetailDette;
use App\Entity\Dette;
use App\Repository\ClientRepository;
use App\Repository\DetteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DetteController extends AbstractController
{


    #[Route('/dettestore', name: 'app_article1')]
    public function index(Request $request, SessionInterface $session, ArticleRepository $articleRepository): Response
    {

        // session_destroy();
        $dette = new Dette();

        // Vérifie si la requête est POST
        if ($request->isMethod('POST')) {
            $data = $request->request->all(); // Récupère les données textuelles


            // Validation des champs obligatoires
            if (empty($data['clientId']) || empty($data['articleId']) || empty($data['montant'])) {
                return new Response("Les champs CLIENT, ARTICLE et MONTANT sont obligatoires.", 400);
            }

            $panier = $session->get('panier', []);

            $article = $articleRepository->find($data['articleId']);
            $clientId = $data['clientId'];

            if ($article) {
                $panier[] = [
                    'clientId' => $clientId,
                    'libelle' => $article->getLibelle(),
                    'id' => $article->getId(),
                    'prix' => $article->getPrix(),
                    'quantity' => $data['montant'],
                    'total' => $article->getPrix() * $data['montant'],

                ];

                $session->set('panier', $panier);
                // session_destroy();
            }
        }
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/dette/form.html');
        // Retourner la vue HTML
        return new Response($htmlContent);
    }


    #[Route('/dette/save', name: 'app_article')]
    public function save(Request $request, SessionInterface $session, ClientRepository $clientRepository, EntityManagerInterface $em, ArticleRepository $articleRepository): Response
    {
        $panier = $session->get('panier', []);
        $clientId = $panier[0]['clientId'];
        $dette = new Dette();
        $client = $clientRepository->find($clientId);
        if (!$client) {
            return new Response("Client non trouvé", 404);
        }

        $dette->setClient($client);
        $total = 0;
        foreach ($panier as $item) {
            $article = $articleRepository->find($item['id']);
            $detail = new DetailDette();
            $detail->setArticleId($article);
            $detail->setDetteId($dette);
            $detail->setQte($item['quantity']);
            $em->persist($detail);
            $total += $item['total'];
        }
        $dette->setMontant($total);
        $dette->setMontantVerser(0);
        $em->persist($dette);
        $em->flush();
        $session->remove('panier');
        return new Response("Dette enregistrée avec succès.", 200);
    }


    #[Route('/api/panier', name: 'api_get_panier', methods: ['GET'])]
    public function getPanier(SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);

        return new JsonResponse($panier);
    }

    // #[Route('/api/dette', name: 'api_dette')]
    // public function apiArticle(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    // {
    //     // Récupérer la page courante depuis la requête (par défaut, page 1)
    //     $page = $request->query->getInt('page', 1);
    //     $limit = 5;  // Nombre d'articles par page

    //     // Créer une requête pour récupérer tous les articles
    //     $queryBuilder = $articleRepository->createQueryBuilder('a');

    //     // Utiliser KNP Paginator pour paginer les résultats
    //     $pagination = $paginator->paginate(
    //         $queryBuilder,   // La requête
    //         $page,           // La page actuelle
    //         $limit           // Nombre d'articles par page
    //     );

    //     // Extraire les articles et les informations de pagination
    //     $articles = $pagination->getItems();
    //     $totalItems = $pagination->getTotalItemCount();
    //     $totalPages = $pagination->count();

    //     // Préparer les données à retourner
    //     $data = [
    //         'articles' => array_map(function ($article) {
    //             return [
    //                 'id' => $article->getId(),
    //                 'libelle' => $article->getLibelle(),
    //                 'prix' => $article->getPrix(),
    //                 'qteStock' => $article->getQteStock(),
    //             ];
    //         }, $articles),
    //         'pagination' => [
    //             'current_page' => $page,
    //             'total_pages' => $totalPages,
    //             'total_items' => $totalItems,
    //         ]
    //     ];

    //     // Retourner les données sous forme de JSON
    //     return $this->json($data);
    // }



    #[Route('/dette', name: 'app_client')]
    public function index1(): Response
    {
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/dette/index.html');
        // Retourner la vue HTML


        return new Response($htmlContent);
    }



    #[Route('/api/dette', name: 'api_detteliste')]
    public function apiListeDette(DetteRepository $detteRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {

        $page = $request->query->getInt('page', 1);
        $limit = 5;


        $queryBuilder = $detteRepository->findAll();

        // Utiliser KNP Paginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,   // La requête
            $page,           // La page actuelle
            $limit           // Nombre d'articles par page
        );

        // Extraire les articles et les informations de pagination
        $dettes = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $totalPages = $pagination->count();

        // Préparer les données à retourner
        $data = [
            'dettes' => array_map(function ($dette) {

                return [
                    'id' => $dette->getId(),
                    'client' => $dette->getClient()->getSurname(),
                    'montant' => $dette->getMontant(),
                    'montantVerser' => $dette->getMontantVerser(),
                    'dateCreation' => $dette->getCreateAt(),
                    'statut' => $dette->getEtatDette(),
                ];
            }, $dettes),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
            ]
        ];

        // Retourner les données sous forme de JSON
        return $this->json($data);
    }

    /*************  ✨ Codeium Command ⭐  *************/
    /**
     * Store a new article.
     *
     * @Route("/article/store", name="article.store", methods={"POST", "GET"})
     */
    /******  e0dce5be-09f6-4088-a085-a286b76c95f9  *******/
    #[Route('/dette/store', name: 'article.store', methods: ['POST', 'GET'])]


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
