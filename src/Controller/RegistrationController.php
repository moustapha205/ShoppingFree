<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;// pour gérer les requêtes HTTP, notamment pour récupérer les données du formulaire d'inscription soumis par l'utilisateur.
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();// on crée une nouvelle instance de l'entité User pour représenter le nouvel utilisateur qui va être enregistré dans la base de données. Cette instance servira de modèle pour le formulaire d'inscription et contiendra les données saisies par l'utilisateur.
        $form = $this->createForm(RegistrationFormType::class, $user);// on crée un formulaire d'inscription en utilisant la classe RegistrationFormType, qui est une classe de formulaire personnalisée que vous avez créée pour gérer les champs d'inscription. En passant l'instance $user comme deuxième argument, vous liez le formulaire à cette instance, ce qui permet de remplir automatiquement les propriétés de l'entité User avec les données saisies par l'utilisateur lors de la soumission du formulaire.
        $form->handleRequest($request);//pour vérifier si le formulaire a été soumis et pour traiter les données envoyées par l'user  .

        if ($form->isSubmitted() && $form->isValid()) {// si le formulaire est soumis et valide apres la verification de la securité (tokencsrf correspond toujours )
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();// on récupère le mot de passe en clair saisi par l'utilisateur dans le champ 'plainPassword' du formulaire. Cette variable contiendra le mot de passe que l'utilisateur a entré, avant qu'il ne soit haché pour des raisons de sécurité.

            
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));// on utilise le service UserPasswordHasherInterface pour hacher le mot de passe en clair. La méthode hashPassword prend l'entité User et le mot de passe en clair comme arguments, et retourne une version hachée du mot de passe qui peut être stockée en toute sécurité dans la base de données. En appelant $user->setPassword(), on assigne ce mot de passe haché à l'entité User, ce qui garantit que le mot de passe de l'utilisateur est protégé contre les accès non autorisés.

            $entityManager->persist($user);// pour dire à Doctrine de prendre en compte cette nouvelle entité User et de la préparer à être enregistrée en base de données lors du prochain flush(). Cela permet de gérer les transactions de manière efficace et d'assurer l'intégrité des données en regroupant plusieurs opérations d'enregistrement en une seule transaction.
            $entityManager->flush();// pour exécuter toutes les opérations d'enregistrement en attente et mettre à jour la base de données.

            // faire ici tout ce que je veux après l'inscription, comme envoyer un email de bienvenue, etc.

            return $this->redirectToRoute('app_home');//après l'inscription, on redirige l'utilisateur vers la page d'accueil ou une autre page de son choix. Cela permet de lui fournir un retour d'information sur le succès de son inscription et de l'orienter vers la suite de son expérience sur le site.
        }
//et si c'est pas valide ou pas soumis alors on affiche le formulaire d'inscription pour que l'utilisateur puisse le remplir et s'inscrire sur le site.
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);// on affiche le formulaire d'inscription en rendant le template 'registration/register.html.twig' et en lui passant la variable 'registrationForm' qui contient le formulaire à afficher. Cela permet de présenter le formulaire d'inscription à l'utilisateur pour qu'il puisse saisir ses informations et s'inscrire sur le site.
    }
}
