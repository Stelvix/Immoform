<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;


final class VoirDemandeFormationsController extends AbstractController
{
        public function __construct(private HttpClientInterface $httpClient)
        {
        }

        // je creer une focntion pour récuperer les demandes de formations en fonction de l'id du contact connecté
        private function getDemandeFormationById(int $ContactID): array
        {

            $formation =[];

            $url = $_ENV["API_GET_FORMATION_BY_CONTACT_ID"] . $ContactID;

            try{
                $response = $this->httpClient->request('GET', $url);
                if($response->getStatusCode() === 200){
                    return $response->toArray();
                }
            } catch(\Exception $e){
throw new \Exception('Impossible de récupérer les formations');            }

            return [];
        }

    #[Route('/voir-demande-formations', name: 'app_voir_demande_formations')]
    public function index(Request $request): Response
    {

     // on get l'id du contact courant depuis la session
    $session = $request->getSession();

    $ContactID = $session->get('contactID');

    if(!$ContactID){
        $this->addFlash(
           'error',
           "Impossible d'afficher vos demandes"
        );
        return $this->redirectToRoute('app_welcome');
    }
        $formation = $this->getDemandeFormationById($ContactID);

        if(empty($formation)){
            $this->addFlash(
                'error',
                "Aucune demande de formation trouvée"
            );
            return $this->redirectToRoute('app_welcome');
        }
        return $this->render('voir_demande_formations/index.html.twig', [
            'formations' => $formation,
         ]);
    }
}
