<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;
use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ProduitRecherche;
use App\Form\ProduitRechercheType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Recette;                                                         
use App\Repository\RecetteRepository;                                           

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
	 * @Route("/produit/categoriecards/{categorie<\d+>}", name="produit_categoriecards")
     */
    public function index($categorie = null, Request $request, ProduitRepository $repository, SessionInterface $session, PaginatorInterface $paginator)
    {	
		// si 2e route alors $categorie est renseigné et on afficher que les produits de cette categorie
		if ($categorie != null) {
			$lesProduits= $paginator->paginate(
                $repository->findAllByCategorie($categorie),
                $request->query->getint('page',1),
                5
            );
			$formRechercheView = null;
		} else
		{
			// créer l'objet et le formulaire de recherche
			$produitRecherche = new ProduitRecherche();
			$formRecherche = $this->createForm(ProduitRechercheType::class, $produitRecherche);
			$formRecherche->handleRequest($request);
			if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
				$produitRecherche = $formRecherche->getData();
				// cherche les produits correspondant aux critères, triés par libellé
				// requête construite dynamiquement alors il est plus simple d'utiliser le querybuilder
				//$lesProduits =$repository->findAllByCriteria($produitRecherche);
				// mémoriser les critères de sélection dans une variable de session
				$session->set('ProduitCriteres', $produitRecherche);
				$lesProduits= $paginator->paginate(
					$repository->findAllByCriteria($produitRecherche),
					$request->query->getint('page',1),
					5
				);
			} else {
				// lire les produits
				if ($session->has("ProduitCriteres")) {
					$produitRecherche = $session->get("ProduitCriteres");
					//$lesProduits = $repository->findAllByCriteria($produitRecherche);
					$lesProduits= $paginator->paginate(
						$repository->findAllByCriteria($produitRecherche),
						$request->query->getint('page',1),
						5
					);
					$formRecherche = $this->createForm(ProduitRechercheType::class, $produitRecherche);
					$formRecherche->setData($produitRecherche);
				} else {
					//$lesProduits = $repository->findAllOrderByLibelle();
					$p=new ProduitRecherche();
					$lesProduits= $paginator->paginate(
						$repository->findAllByCriteria($p),
						$request->query->getint('page',1),
						5
					);
				}
			}
			$formRechercheView = $formRecherche->createView() ;
		}
        return $this->render('produit/index.html.twig', [
			'formRecherche' => $formRechercheView,
            'lesProduits' => $lesProduits,
        ]);
    }
	
	/**
	 * @Route("/produit/ajouter", name="produit_ajouter")
	 */
	public function ajouter(Produit $produit=null, Request $request, EntityManagerInterface $entityManager)
	{
		$form = $this->createForm(ProduitType::class, $produit);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			// cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
			$produit = $form->getData();
			// on met à jour la base de données 
			$entityManager->persist($produit);
			$entityManager->flush();
			$this->addFlash(
				'success',
				'Le produit ' . $produit->getLibelle() . ' a été ajouté.'
			);
			return $this->redirectToRoute('produit');
		} else {
			// cas où l'utilisateur a demandé l'ajout, onaffiche le formulaire d'ajout
			return $this->render('produit/ajouter.html.twig', [
				'form' => $form->createView(),
			]);
		}
	}
	
	/**
	 * @Route("/produit/modifier/{id<\d+>}", name="produit_modifier")
	 */
	public function modifier(Produit $produit = null, Request $request, EntityManagerInterface $entityManager)
	{
		$form = $this->createForm(ProduitType::class, $produit);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// cas où le formulaire  a été soumis par l'utilisateur et est valide
			//pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
			$entityManager->flush();
			$this->addFlash(
				'success',
				'Le produit '.$produit->getLibelle().' a été modifié.'
			);

			return $this->redirectToRoute('produit');
		}
		// cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
		return $this->render('produit/modifier.html.twig', [
			'form' => $form->createView(),
		]);
	}
	
	/**
     * @Route("/produit/supprimer/{id<\d+>}", name="produit_supprimer")
     */
    public function supprimer(Produit $produit, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('action-item'.$produit->getId(), $request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le produit '.$produit->getLibelle().' a été supprimé.'
            );

            return $this->redirectToRoute('produit');
        }
    }
	
	/**
	 * @Route("/produit/ajaxrecettesproduit", name="ajax_recettes_produit")
	 */
	public function ajaxRecettesProduit(Request $request, RecetteRepository $repository)
	{
		// récupérer la valeur de idProduit envoyée
		$idProduit = $request->request->get('idProduit');
		// chercher les recettes correspondantes
		$lesRecettes= $repository->findNameByProduit($idProduit);
		// retourner une réponse encodée en JSON
		$response = new Response(json_encode($lesRecettes));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}