<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\LoginType;
use App\Repository\UsersRepository;


final class LoginController extends AbstractController
{


     public function __construct(private UsersRepository $usersRepo)
     {}

    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        // formulaire de connexion
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $email = $data["email"];
            $password = $data["password"];


            $user = $this->usersRepo->findOneBy(['email' => $email]);

         if (!$user) {
            $this->addFlash('error', 'Email non trouvé');

            }  elseif (!password_verify($password, $user->getPassword())) {
            $this->addFlash('error', 'Mot de passe incorrect');

            } else {
            // Connexion réussie
            // Stockage des informations de l'utilisateur dans la session, mêmem les infos du contact venant de l'API
            $session = $request->getSession();
            $session->set('userName', $user->getName());
            $session->set('userEmail', $user->getEmail());
            $session->set('contactID', $user->getUsersIDsession());

            // je get le contactID que j'ai mis en bdd et je le stocke en session pour les prochaines requetes à l'API     
             $this->addFlash('success', 'Connexion réussie !');
            return $this->redirectToRoute('app_welcome');
            }

            // Si erreur → retourne sur login
            return $this->redirectToRoute('app_login');
        }


        return $this->render('login/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
