<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response// authenticationUtils est un service de Symfony qui fournit des méthodes pour gérer les erreurs d'authentification et récupérer le dernier nom d'utilisateur saisi. En l'injectant dans la méthode login(), on peut facilement accéder à ces fonctionnalités pour afficher les messages d'erreur appropriés et pré-remplir le champ du nom d'utilisateur dans le formulaire de connexion.
    {//authenticationUtils gère les erreurs de connexion
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();// pour récupérer la dernière erreur d'authentification, s'il y en a une. Si l'utilisateur a tenté de se connecter avec des informations d'identification incorrectes, cette méthode renverra une instance de l'erreur qui peut être affichée dans le template de connexion pour informer l'utilisateur du problème.
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();// pour récupérer le dernier nom d'utilisateur saisi par l'utilisateur lors de sa tentative de connexion. Cela permet de pré-remplir le champ du nom d'utilisateur dans le formulaire de connexion, offrant ainsi une meilleure expérience utilisateur en évitant à l'utilisateur de devoir ressaisir son nom d'utilisateur après une erreur de connexion.

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }// pour afficher le formulaire de connexion en rendant le template 'security/login.html.twig' et en lui passant les variables 'last_username' et 'error' qui contiennent respectivement le dernier nom d'utilisateur saisi et l'erreur d'authentification, s'il y en a une. Cela permet de fournir un retour d'information à l'utilisateur sur les erreurs de connexion et de pré-remplir le champ du nom d'utilisateur pour faciliter la correction des erreurs.

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('cette methode peut etre vide car la deconnexion est gérée automatiquement par symfony via security.yaml.');// pour gérer la déconnexion de l'utilisateur. Cette méthode est intentionnellement laissée vide, car la déconnexion est gérée par le système de sécurité de Symfony en interceptant la route définie pour la déconnexion (app_logout) et en effectuant les actions nécessaires pour déconnecter l'utilisateur, comme effacer la session, etc. En lançant une exception ici, on indique clairement que cette méthode ne doit pas être exécutée directement, mais qu'elle est simplement un point d'entrée pour le système de sécurité.
    }
}
