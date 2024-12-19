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
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserController extends AbstractController
{


    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/user/index.html');
        // Retourner la vue HTML


        return new Response($htmlContent);
    }

    #[Route('/api/user', name: 'api_user')]
    public function apiUser(UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 4;  // Nombre d'articles par page

        // Créer une requête pour récupérer tous les articles
        $queryBuilder = $usersRepository->findAll();

        // Utiliser KNP Paginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,   // La requête
            $page,           // La page actuelle
            $limit           // Nombre d'articles par page
        );

        // Extraire les articles et les informations de pagination
        $users = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $totalPages = $pagination->count();

        // Préparer les données à retourner
        $data = [
            'users' => array_map(function ($user) {

                return [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'login' => $user->getLogin(),

                    // Ajouter cette ligne pour inclure le nom d'utilisateur

                ];
            }, $users),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
            ]
        ];

        // Retourner les données sous forme de JSON
        return $this->json($data);
    }
}
