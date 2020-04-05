<?php

namespace App\Controller;

use App\Form\ClientType;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ClientRecherche;
use App\Form\ClientRechercheType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="client")
	 * @Route("/client/demandermodification/{id<\d+>}", name="client_demandermodification")
	 *
     * @param null $id
     * @param ClientRepository $repository
     * @param Request $request
	 * @param SessionInterface $session
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($id = null, ClientRepository $repository, Request $request, SessionInterface $session, PaginatorInterface $paginator)
    {
		// créer l'objet et le formulaire de création
        $client = new Client();
        $formCreation = $this->createForm(ClientType::class, $client);
		
		// si 2e route alors $id est renseigné et on  crée le formulaire de modification
		if ($id != null) {
			// sécurité supplémentaire, on vérifie le token
			if ($this->isCsrfTokenValid('action-item'.$id, $request->get('_token'))) {
				$clientModif = $repository->find($id);   // le client à modifier
				$formModificationView = $this->createForm(ClientType::class, $clientModif)->createView();
			}
        } else {
            $formModificationView = null;
        }
		
		// créer l'objet et le formulaire de recherche
        $clientRecherche = new ClientRecherche();
        $formRecherche = $this->createForm(ClientRechercheType::class, $clientRecherche);
        $formRecherche->handleRequest($request);
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $clientRecherche = $formRecherche->getData();
			// mémoriser les critères de sélection dans une variable de session
            $session->set('ClientCriteres', $clientRecherche);
			$lesClients= $paginator->paginate(
                $repository->findAllByCriteria($clientRecherche),
                $request->query->getint('page',1),
                5
            );
        } else {
			// lire les clients
            if ($session->has("ClientCriteres")) {
                $clientRecherche = $session->get("ClientCriteres");
                //$lesClients = $repository->findAllByCriteria($clientRecherche);
				$lesClients= $paginator->paginate(
					$repository->findAllByCriteria($clientRecherche),
					$request->query->getint('page',1),
					5
				);
                $formRecherche = $this->createForm(ClientRechercheType::class, $clientRecherche);
                $formRecherche->setData($clientRecherche);
            } else {
                //$lesClients = $repository->findAllOrderByLibelle();
				$c=new ClientRecherche();
                $lesClients= $paginator->paginate(
                    $repository->findAllByCriteria($c),
                    $request->query->getint('page',1),
                    5
                );
			}
        }
		
        return $this->render('client/index.html.twig', [
            'lesClients' => $lesClients,
			'formRecherche' => $formRecherche->createView(),
			'formCreation' => $formCreation->createView(),
			'formModification' => $formModificationView,
            'idClientModif' => $id,
        ]);
    }
	
	/**
	 * @Route("/client/ajouter", name="client_ajouter")
	 */
	public function ajouter(Client $client = null, Request $request, EntityManagerInterface $entityManager, ClientRepository $repository)
	{
		//  $client objet de la classe Client, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
		//  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
		//  $entityManager  pour la persistance des données

		// création d'un formulaire de type ClientType
		$client = new Client();
		$form = $this->createForm(ClientType::class, $client);

		// handleRequest met à jour le formulaire
		//  si le formulaire a été soumis, handleRequest renseigne les propriétés
		//      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// c'est le cas du retour du formulaire
			//         l'objet $client a été automatiquement "hydraté" par Doctrine
			dump($client);
			// dire à Doctrine que l'objet sera (éventuellement) persisté
			$entityManager->persist($client);
			// exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
			$entityManager->flush();
			// ajouter un message flash de succès pour informer l'utilisateur
			$this->addFlash(
				'success',
				'Le/La client(e) ' . $client->getNom() . ' ' . $client->getPrenom() . ' a été ajouté(e).'
			);
			// rediriger vers l'affichage des clients qui comprend le formulaire pour l'ajout d'un nouveau client
			return $this->redirectToRoute('client');

		} else {
	// affichage de la liste des clients avec le formulaire de création et ses erreurs
			// lire les clients
			$lesClients = $repository->findAll();
			// rendre la vue
			return $this->render('client/index.html.twig', [
				'formCreation' => $form->createView(),
				'lesClients' => $lesClients,
				'formModification' => null,
				'idClientModif' => null,
			]);
		}
	}
	
	/**
	 * @Route("/client/modifier/{id<\d+>}", name="client_modifier")
	 */
	public function modifier(Client $client = null, $id = null, Request $request, EntityManagerInterface $entityManager, ClientRepository $repository)
	{
		//  Symfony 4 est capable de retrouver le client à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
		$form = $this->createForm(ClientType::class, $client);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			// va effectuer la requête d'UPDATE en base de données
			// pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
			$entityManager->flush();
			$this->addFlash(
				'success',
				'Le/La client(e) '. $client->getNom() . ' ' . $client->getPrenom() .' a été modifié(e).'
			);
			// rediriger vers l'affichage des clients qui comprend le formulaire pour l'ajout d'un nouveau client
			return $this->redirectToRoute('client');

		} else {
			// affichage de la liste des clients avec le formulaire de modification et ses erreurs
			// créer l'objet et le formulaire de création
			$client = new Client();
			$formCreation = $this->createForm(ClientType::class, $client);
			// lire les clients
			$lesClients = $repository->findAll();
			// rendre la vue
			return $this->render('client/index.html.twig', [
				'formCreation' => $formCreation->createView(),
				'lesClients' => $lesClients,
				'formModification' => $form->createView(),
				'idClientModif' => $id,
			]);
		}
	}
	
	/**
	 * @Route("/client/supprimer/{id<\d+>}", name="client_supprimer")
	 */
	public function supprimer(Client $client = null, Request $request, EntityManagerInterface $entityManager)
	{
		 // vérifier le token
		if ($this->isCsrfTokenValid('action-item'.$client->getId(), $request->get('_token'))) {
			// supprimer le client
			$entityManager->remove($client);
			$entityManager->flush();
			$this->addFlash(
				'success',
				'Le/La client(e) ' . $client->getNom() . ' ' . $client->getPrenom() . ' a été supprimé(e).'
			);
		}
		return $this->redirectToRoute('client');
	}
}
