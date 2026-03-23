<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\LoginType;
use App\Repository\UsersRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


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

            // On récupère l'email de l'uitlisateur

            $user = $this->usersRepo->findOneBy(['email' => $email]);

            if(!$user){
                $this->addFlash(
                   'error',
                   'Email non trouvé'
                );
                return $this->redirectToRoute('app_login');
            } elseif(password_verify($password, $user->getPassword())){
                $this->addFlash(
                   'success',
                   'Connexion reussie'
                );
                    // On stocke le nom de l'utilisateur dans la session pour l'afficher sur la page d'accueil
                    $session = $request->getSession();
                    $session->set('userName', $user->getName());
                 return $this->redirectToRoute('app_welcome');
            } else{
                $this->addFlash(
                   'error',
                   'Mot de passe incoorect'
                );
                return $this->redirectToRoute('app_login');
            }
        }
        

        return $this->render('login/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
