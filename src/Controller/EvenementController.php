<?php

namespace App\Controller;

use App\Form\EvenementType;
use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement")
	 * @Route("/evenement/demandermodification/{id<\d+>}", name="evenement_demandermodification")
     */
    public function index($id = null, EvenementRepository $repository, Request $request)
    {
		// créer l'objet et le formulaire de création
        $evenement = new Evenement();
        $formCreation = $this->createForm(EvenementType::class, $evenement);
		
		// si 2e route alors $id est renseigné et on  crée le formulaire de modification
		if ($id != null) {
			// sécurité supplémentaire, on vérifie le token
			if ($this->isCsrfTokenValid('action-item'.$id, $request->get('_token'))) {
				$evenementModif = $repository->find($id);   // l'événement à modifier
				$formModificationView = $this->createForm(EvenementType::class, $evenementModif)->createView();
			}
        } else {
            $formModificationView = null;
        }
		
		//lire les événements
		$lesEvenements = $repository->findAll();
        return $this->render('evenement/index.html.twig', [
            'lesEvenements' => $lesEvenements,
			'formCreation' => $formCreation->createView(),
			'formModification' => $formModificationView,
            'idEvenementModif' => $id,
        ]);
    }
	
	/**
	 * @Route("/evenement/ajouter", name="evenement_ajouter")
	 */
	public function ajouter(Evenement $evenement = null, Request $request, EntityManagerInterface $entityManager, EvenementRepository $repository)
	{
		//  $evenement objet de la classe Evenement, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
		//  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
		//  $entityManager  pour la persistance des données

		// création d'un formulaire de type EvenementType
		$evenement = new Evenement();
		$form = $this->createForm(EvenementType::class, $evenement);

		// handleRequest met à jour le formulaire
		//  si le formulaire a été soumis, handleRequest renseigne les propriétés
		//      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// c'est le cas du retour du formulaire
			//         l'objet $evenement a été automatiquement "hydraté" par Doctrine
			dump($evenement);
			// dire à Doctrine que l'objet sera (éventuellement) persisté
			$entityManager->persist($evenement);
			// exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
			$entityManager->flush();
			// ajouter un message flash de succès pour informer l'utilisateur
			$this->addFlash(
				'success',
				'L\'événement ' . $evenement->getTitre() . ' a été ajouté.'
			);
			// rediriger vers l'affichage des événements qui comprend le formulaire pour l'ajout d'un nouvel événement
			return $this->redirectToRoute('evenement');

		} else {
	// affichage de la liste des événements avec le formulaire de création et ses erreurs
			// lire les événements
			$lesEvenements = $repository->findAll();
			// rendre la vue
			return $this->render('evenement/index.html.twig', [
				'formCreation' => $form->createView(),
				'lesEvenements' => $lesEvenements,
				'formModification' => null,
				'idEvenementModif' => null,
			]);
		}
	}
	
	/**
	 * @Route("/evenement/modifier/{id<\d+>}", name="evenement_modifier")
	 */
	public function modifier(Evenement $evenement = null, $id = null, Request $request, EntityManagerInterface $entityManager, EvenementRepository $repository)
	{
		//  Symfony 4 est capable de retrouver l'événement à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
		$form = $this->createForm(EvenementType::class, $evenement);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			// va effectuer la requête d'UPDATE en base de données
			// pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
			$entityManager->flush();
			$this->addFlash(
				'success',
				'L\'événement '.$evenement->getTitre().' a été modifié.'
			);
			// rediriger vers l'affichage des événements qui comprend le formulaire pour l"ajout d'un nouvel événement
			return $this->redirectToRoute('evenement');

		} else {
			// affichage de la liste des événements avec le formulaire de modification et ses erreurs
			// créer l'objet et le formulaire de création
			$evenement = new Evenement();
			$formCreation = $this->createForm(EvenementType::class, $evenement);
			// lire les événements
			$lesEvenements = $repository->findAll();
			// rendre la vue
			return $this->render('evenement/index.html.twig', [
				'formCreation' => $formCreation->createView(),
				'lesEvenements' => $lesEvenements,
				'formModification' => $form->createView(),
				'idEvenementModif' => $id,
			]);
		}
	}
	
	/**
	 * @Route("/evenement/supprimer/{id<\d+>}", name="evenement_supprimer")
	 */
	public function supprimer(Evenement $evenement = null, Request $request, EntityManagerInterface $entityManager)
	{
		 // vérifier le token
		if ($this->isCsrfTokenValid('action-item'.$evenement->getId(), $request->get('_token'))) {
			if (date("d/m/Y") == date_format($evenement->getDate(), 'd/m/Y') && (date("H:i") >= date_format($evenement->getHeureDebut(), 'H:i') && date("H:i") <= date_format($evenement->getHeureFin(), 'H:i'))) {
				$this->addFlash(
					'error',
					'L\'événement ' . $evenement->getTitre() . ' est en cours, il ne peut pas être supprimé.'
				);
				return $this->redirectToRoute('evenement');
			}
			// supprimer l'événement
			$entityManager->remove($evenement);
			$entityManager->flush();
			$this->addFlash(
				'success',
				'L\'événement ' . $evenement->getTitre() . ' a été supprimé.'
			);
		}
		return $this->redirectToRoute('evenement');
	}
}






