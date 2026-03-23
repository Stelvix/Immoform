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







 final class SignUpController extends AbstractController
 {
     public function __construct(private HttpClientInterface $httpClient, EntityManagerInterface $Entity )
     {
     }

     public function getContactInformation(): array
     {
         $response = $this->httpClient->request(
             'GET',
             'http://172.16.126.1/api/Contact'
         );

         $statusCode = $response->getStatusCode();
         if ($statusCode === 200) {
             $content = $response->toArray();
         }
         return $content;

     }

     #[Route('/signup', name: 'app_sign_up')]
public function index(Request $request): Response
{
    $form = $this->createForm(UserType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $donnes = $form->getData();
        $emailForm = $donnes->getEmail();

        $contacts = $this->getContactInformation();

        foreach ($contacts as $contact) {

            // vérification email API
            if ($emailForm === $contact["email"]) {

                $this->addFlash('success', 'Email reconnu, inscription autorisée');

                    // Enregistrement de l'utilisateur dans la base de données
                    $user = new Users();
                    $user->setEmail($emailForm);
                    $Entity->persist($user);
                    $Entity->flush();

                    return $this->redirectToRoute('app_login');
                }
            }

        //aucun email trouvé
        $this->addFlash('error', 'Email non autorisé');
    }

    return $this->render('sign_up/index.html.twig', [
        'form' => $form->createView(),
    ]);
}

}

 