<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Form\UserType;
// Ajoute du repository
use App\Repository\ContactRepository;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



 final class SignUpController extends AbstractController
 {
    private EntityManagerInterface $entityManager;


     public function __construct(private HttpClientInterface $httpClient, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
     {
        $this->entityManager = $entityManager;
     }

     public function getContactInformation(): array
     {
        $url = $_ENV["API_URL_CONTACT"] ;

        if (!$url) {
            throw new \RuntimeException("Erreur de l'URL de l\'API de contact. Veuillez vérifier votre configuration.");
        }

         $response = $this->httpClient->request('GET', $url);

         if ($response->getStatusCode() === 200) {
            return $response->toArray();
         }
         return [];

     }

#[Route('/signup', name: 'app_sign_up')]
public function signup(Request $request): Response
{
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnes = $form->getData();
            $emailForm = $donnes->getEmail();

            $contacts = $this->getContactInformation();
            // on creer un booléen pour vérifier si l'email existe dans la base de données
            $found = false;
            foreach ($contacts as $contact) {

                // vérification email API
              if($contact['email'] === $emailForm){
                $found = true;
                break;
              }
            }
            if($found){
                    $user = new Users();
                    $user->setEmail($emailForm);

                    // Hashage du mot de passe
                    $hashedPassword = password_hash($donnes->getPassword(), PASSWORD_BCRYPT);

                    $user->setPassword($hashedPassword);
                    $user->setName($donnes->getName());
                    $user->setLname($donnes->getLname());

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    $this->addFlash('success', 'Inscription réussie !');
                    // je récupère l'id du contact pour le stocker dans la session

                    $session = $request->getSession();
                    $session->set('contactID', $contact['contactID']);

                    dd($session->get('contactID')); // Debug pour vérifier que le contactID est bien stocké en session

                    return $this->redirectToRoute('app_login');
                } else {
                    $this->addFlash('error', 'Email non trouvé dans la base de données.');
                    return $this->redirectToRoute('app_sign_up');
                }
        }

    return $this->render('sign_up/index.html.twig', [
        'form' => $form->createView(),
        ]);
    }

}


