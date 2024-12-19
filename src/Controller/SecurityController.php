<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer les erreurs de connexion (s'il y en a)
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $error = $error->getMessage();
            dd($error);
        }
        // Dernier identifiant saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Créer le formulaire HTML (pas de Twig)
        $formHtml = "
            <form method='post' action='/login'>
                <div>
                    <label for='username'>Nom d'utilisateur</label>
                    <input type='text' id='username' name='_username' value='" . htmlspecialchars($lastUsername) . "' required>
                </div>
                <div>
                    <label for='password'>Mot de passe</label>
                    <input type='password' id='password' name='_password' required>
                </div>
                <button type='submit'>Se connecter</button>
                " . ($error ? "<p style='color:red;'>Erreur : " . $error->getMessage() . "</p>" : "") . "
            </form>
            ";

        return new Response($formHtml);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Ce point de déconnexion sera intercepté par le firewall.');
    }
}
