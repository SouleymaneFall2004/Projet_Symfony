<?php

namespace App\Controller;

use App\Repository\DetteRepository;
use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Paiement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PaiementController.php',
        ]);
    }
    #[Route('/client/dette/paiement/id={id}', name: 'app_client_dette_paiement')]
    public function clientDettePaiement(PaiementRepository $paiementRepository, $id): Response
    {

        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/paiement/form.html');
        // Retourner la vue HTML
        return new Response($htmlContent);
    }

    #[Route('ListPaiement/id={id}', name: 'app_paiement1')]
    public function paiement(PaiementRepository $paiementRepository): Response
    {
        // Charger la vue HTML depuis le fichier
        $htmlContent = file_get_contents('/home/bouba/Documents/gestion_dette_distribue/gestion_dette_distribue/src/Views/paiement/index.html');
        // Retourner la vue HTML
        return new Response($htmlContent);
    }

    // /api/paiement/id=${id}&page=${page}
    #[Route('/api/paiement/id={id}', name: 'api_paiement')]
    public function apiPaiement(PaiementRepository $paiementRepository, Request $request, PaginatorInterface $paginator, $id): Response
    {
        $id = (int)$id;
        $page = $request->query->getInt('page', 1);
        $limit = 5;
        $paiement = $paiementRepository->findPaiementInDette($id);


        $pagination = $paginator->paginate(
            $paiement,
            $page,
            $limit
        );

        $paiemants = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $totalPages = $pagination->count();

        $data = [
            'paiements' => array_map(function ($paiemant) {
                return [
                    'id' => $paiemant->getId(),
                    'montant' => $paiemant->getMontant(),
                    'client' => $paiemant->getDette()->getClient()->getSurname(),


                ];
            }, $paiemants),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
            ]
        ];

        return $this->json($data);
    }

    #[Route('/paiement/store/id={id}', name: 'app_paiement_store')]
    public function paiementStore(Request $request, DetteRepository $detteRepository, PaiementRepository $paiementRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $data = $request->request->all();
        $dette =  $detteRepository->find($id);
        $client = $dette->getClient();
        $paiement = new Paiement();
        $paiement->setMontant($data['montant']);
        $paiement->setClient($client);
        // $paiement->setCreateAt();
        $paiement->setDette($dette);
        $dette->setMontantVerser($dette->getMontantVerser() + $paiement->getMontant());
        $dette->$entityManager->persist($paiement);
        $entityManager->flush();

        return new Response();
    }
}
