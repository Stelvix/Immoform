<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;


final class WelcomeController extends AbstractController
{
    #[Route('/welcome', name: 'app_welcome')]
    public function welcome(Request $request): Response
    {

    // je récupère la session de l'utilisateur pour afficher son nom dans la page d'accueil
    $session = $request->getSession();
    $userName = $session->get('userName');
 

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'userName' => $userName,
            
         ]);
    }
}
