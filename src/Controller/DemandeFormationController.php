<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\DemandeFormationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DemandeFormationController extends AbstractController
{
    public function __construct(private HttpClientInterface $httpClient)
    {}


    /**
     * Récupère les agences associées à un contact via l'API
     */
    private function getAgences(int $contactID): array
    {
        $agences = [];

        $url = $_ENV["API_GET_AGENCE_BY_CONTACT_ID"] . $contactID;
        try {
            $response = $this->httpClient->request('GET', $url);
            if ($response->getStatusCode() === 200) {
                return $response->toArray();
             }
        } catch (\Exception $e) {
throw new \Exception('Impossible de récupérer les agences');        }

        return [];
    }


    #[Route('/demande-formation', name: 'app_demande_formation')]
    public function demandeFormation(Request $request): Response
    {
        $session = $request->getSession();
        $contactID = $session->get('contactID');

        // Vérification que le contact est bien connecté
        if (!$contactID) {
            $this->addFlash('error', 'Vous devez être connecté pour faire une demande de formation.');
            return $this->redirectToRoute('app_login'); // adapte à ta route login
        }

        $contactID = (int)$contactID; // s'assurer que c'est un int

        // Récupération des agences associées à ce contact
        $agences = $this->getAgences($contactID);

        // Création du formulaire en passant les agences
        $form = $this->createForm(DemandeFormationType::class, null, [
            'agences' => $agences
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['contactID'] = $contactID;
            $data['statutDemande'] = 'En attente';
            $data['formationID'] = null;

            if($data['dateSouhaitee'] instanceof \DateTimeInterface){
                $data['dateSouhaitee'] = $data['dateSouhaitee']->format('Y-m-d');
            }

            try {
                $response = $this->httpClient->request('POST', $_ENV["API_URL_DEMANDE_FORMATION"], [
                    'json' => $data
                ]);

                if ($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'Demande de formation soumise avec succès !');
                    return $this->redirectToRoute('app_welcome');
                } else {
                    $this->addFlash('error', 'Erreur lors de l’envoi : ' . $response->getContent(false));
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de contacter l’API : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_demande_formation');
        }

        return $this->render('demande_formation/index.html.twig', [
            'form' => $form->createView(),
            'agences' => $agences,
        ]);
    }
    
}
