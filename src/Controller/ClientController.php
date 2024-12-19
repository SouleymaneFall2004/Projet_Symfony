<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DTO\ArticleDTO;
use App\Entity\Users;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ClientType;
use App\Repository\DetteRepository;

class ClientController extends AbstractController
{

    #[Route('/clientList', name: 'app_clientList')]
    public function index(ClientRepository $clientRepository): Response
    {
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/client/index.html');
        // Retourner la vue HTML


        return new Response($htmlContent);
    }

    #[Route('/client/dette/id={id}', name: 'client_dette1')]
    public function detteSolde(ClientRepository $clientRepository, $id): Response
    {
        $client = $clientRepository->find($id);
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/dette/index1.html');
        // Retourner la vue HTML
        return new Response($htmlContent);
    }
    #[Route('/api/client/dette/id={id}', name: 'api_client_dette1')]
    public function apiDetteSolde(ClientRepository $clientRepository, $id, Request $request, PaginatorInterface $paginator, DetteRepository $detteRepository): Response
    {
        $id = (int)$id;
        $page = $request->query->getInt('page', 1);
        $limit = 5;
        $client = $clientRepository->find($id);
        $ClientDette = $detteRepository->findDetteClient($client);

        $pagination = $paginator->paginate(
            $ClientDette,
            $page,
            $limit
        );

        $dettes = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $totalPages = $pagination->count();

        $data = [
            'dettes' => array_map(function ($dette) use ($client) {
                return [
                    'id' => $dette->getId(),
                    'montant' => $dette->getMontant(),
                    'client' => $client->getSurname(),
                    'telephone' => $dette->getClient()->getTelephone(),
                    'adresse' => $client->getAdresse(),
                    'montantVerser' => $dette->getMontantVerser(),
                    'dateCreation' => $dette->getCreateAt()->format('Y-m-d'),
                    'statut' => $dette->getEtatDette(),

                ];
            }, $dettes),
            'Client' => [
                'id' => $client->getId(),
                'telephone' => $client->getTelephone(),
                'adresse' => $client->getAdresse(),
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
            ]
        ];

        return $this->json($data);
    }

    #[Route('/api/client', name: 'api_client')]
    public function apiClient(ClientRepository $clientRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        // Récupérer la page courante depuis la requête (par défaut, page 1)
        $page = $request->query->getInt('page', 1);
        $limit = 5;  // Nombre d'articles par page
        $search = $request->query->get('search');
        // Créer une requête pour récupérer tous les articles
        $queryBuilder = $clientRepository->findAll();
        if ($search != null) {
            $queryBuilder = $clientRepository->searchByTelephoneAndName($search);
        }
        // Utiliser KNP Paginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,   // La requête
            $page,           // La page actuelle
            $limit           // Nombre d'articles par page
        );

        // Extraire les articles et les informations de pagination
        $clients = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $totalPages = $pagination->count();

        // Préparer les données à retourner
        $data = [
            'clients' => array_map(function ($client) {

                return [
                    'id' => $client->getId(),
                    'telephone' => $client->getTelephone(),
                    'surnom' => $client->getSurname(),
                    'adresse' => $client->getAdresse(),
                    'email' => $client->getUsers() ? $client->getUsers()->getLogin() : '',
                    'image' => $client->getUsers() ? $client->getUsers()->getBrochureFilename() : '',
                    // Ajouter cette ligne pour inclure le nom d'utilisateur

                ];
            }, $clients),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
            ]
        ];

        // Retourner les données sous forme de JSON
        return $this->json($data);
    }


    #[Route('/store', name: 'app_client1')]
    public function formulaire(ClientRepository $clientRepository): Response
    {
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/client/form.html');

        return new Response($htmlContent);
    }


    #[Route('/clientstore', name: 'app_store')]
    public function store(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Client();

        // Vérifie si la requête est POST
        if ($request->isMethod('POST')) {
            $data = $request->request->all(); // Récupère les données textuelles

            // Validation des champs obligatoires
            if (empty($data['surnom']) || empty($data['adresse']) || empty($data['telephone'])) {
                return new Response("Les champs surnom, adresse et téléphone sont obligatoires.", 400);
            }

            $client->setSurname($data['surnom']);
            $client->setAdresse($data['adresse']);
            $client->setTelephone($data['telephone']);

            if (!empty($data['CreateUser']) && $data['CreateUser'] === 'true') {
                if (empty($data['email']) || empty($data['login']) || empty($data['password'])) {
                    return new Response("Les champs email, login et password sont obligatoires pour créer un utilisateur.", 400);
                }

                // Traitement de l'utilisateur
                $user = new Users();
                $user->setEmail($data['email']);
                $user->setLogin($data['login']);
                $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

                $fileKey = $request->files->get('fileKey');
                if ($fileKey) {
                    $extension = $fileKey->getClientOriginalExtension();
                    $filename = uniqid() . '.' . $extension;
                    $fileKey->move('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/image', $filename);
                }
                $user->setBrochureFilename($filename);
                $roles = $user->getRoles();
                $roles[] = 'ROLE_CLIENT';
                $user->setRoles(array_unique($roles));
                $user->setCreateAt(new \DateTimeImmutable());
                $user->setUpdateAt(new \DateTimeImmutable());

                $entityManager->persist($user);
                $client->setUsers($user);
            }

            $entityManager->persist($client);
            $entityManager->flush();

            // $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/client/index.html');
            // return new Response($htmlContent);

            return $this->redirectToRoute('app_client');
        }

        // // Si la méthode n'est pas POST, retourne le formulaire HTML
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/client/index.html');
        return new Response($htmlContent);
        // return $this->redirectToRoute('app_client');
    }



    // #[Route('/clientstore', name: 'app_store')]
    // public function store(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    // {
    //     $client = new Client();
    //     $form = $this->createForm(ClientType::class, $client);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         if ($form->get('CreateUser')->getData()) {
    //             $email = $form->get('email')->getData();
    //             $login = $form->get('login')->getData();
    //             $password = $form->get('password')->getData();

    //             $user = new Users();
    //             $user->setEmail($email)
    //                 ->setLogin($login)
    //                 ->setPassword($passwordHasher->hashPassword($user, $password))
    //                 ->setCreateAt(new \DateTimeImmutable())
    //                 ->setUpdateAt(new \DateTimeImmutable())
    //                 ->setBlocked(false);

    //             $entityManager->persist($user);
    //             $client->setUsers($user);
    //         }

    //         $entityManager->persist($client);
    //         $entityManager->flush();

    //         return new Response('Client ajouté avec succès !');
    //     }

    //     // Génération du formulaire HTML
    //     $formView = $form->createView();
    //     $formHtml = $this->renderFormHtml($formView);

    //     // Charger le fichier HTML et injecter le formulaire
    //     $htmlTemplate = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/client/form.html');
    //     $htmlTemplate = str_replace('{{ form }}', $formHtml, $htmlTemplate);

    //     return new Response($htmlTemplate);
    // }

    // private function renderFormHtml($formView): string
    // {
    //     // Exemple simplifié pour chaque champ
    //     $html = '<form action="" method="POST">';
    //     foreach ($formView as $child) {
    //         $html .= '<div>';
    //         $html .= $child->vars['label'] ? '<label>' . $child->vars['label'] . '</label>' : '';
    //         if (isset($child->vars['widget'])) {
    //             $html .= $child->vars['widget'];
    //         } else {
    //             // If widget is not available, handle it (optional)
    //             $html .= '<input type="text" name="' . $child->vars['name'] . '" />';
    //         }
    //         $html .= '</div>';
    //     }
    //     $html .= '<button type="submit">Submit</button>';
    //     $html .= '</form>';

    //     return $html;
    // }
}
