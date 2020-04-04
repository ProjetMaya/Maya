<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Entity\FournisseurRecherche;
use App\Form\FournisseurRechercheType;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class FournisseurController extends AbstractController
{
    /**
     * @Route("/fournisseur", name="fournisseur")
     * @Route("/fournisseur/demandermodification/{id<\d+>}", name="fournisseur_demandermodification")
     *
     * @param null $id
     * @param FournisseurRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($id = null, FournisseurRepository $repository, SessionInterface $session, Request $request, PaginatorInterface $paginator)
    {
        // créer l'objet et le formulaire de création
        $fournisseur = new Fournisseur();
        $formCreation = $this->createForm(FournisseurType::class, $fournisseur);

        // créer l'objet et le formulaire de recherche
        $fournisseurRecherche = new FournisseurRecherche();
        $formRecherche = $this->createForm(FournisseurRechercheType::class, $fournisseurRecherche);
        $formRecherche->handleRequest($request);
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $fournisseurRecherche = $formRecherche->getData();
            // cherche les fournisseurs correspondant aux critères, triés par libellé
            // requête construite dynamiquement alors il est plus simple d'utiliser le querybuilder
            //$lesFournisseurs =$repository->findAllByCriteria($fournisseurRecherche);
            // mémoriser les critères de sélection dans une variable de session
            $session->set('FournisseurCriteres', $fournisseurRecherche);
            $lesFournisseurs = $paginator->paginate(
                $repository->findAllByCriteria($fournisseurRecherche),
                $request->query->getint('page', 1),
                5
            );
        } else {
            // lire les fournisseurs
            if ($session->has("FournisseurCriteres")) {
                $fournisseurRecherche = $session->get("FournisseurCriteres");
                //$lesFournisseurs = $repository->findAllByCriteria($fournisseurRecherche);
                $lesFournisseurs = $paginator->paginate(
                    $repository->findAllByCriteria($fournisseurRecherche),
                    $request->query->getint('page', 1),
                    5
                );
                $formRecherche = $this->createForm(FournisseurRechercheType::class, $fournisseurRecherche);
                $formRecherche->setData($fournisseurRecherche);
            } else {
                //$lesFournisseurs = $repository->findAllOrderByLibelle();
                $p = new FournisseurRecherche();
                $lesFournisseurs = $paginator->paginate(
                    $repository->findAllByCriteria($p),
                    $request->query->getint('page', 1),
                    5
                );
            }
        }

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item' . $id, $request->get('_token'))) {
                $fournisseurModif = $repository->find($id);   // le fournisseur à modifier
                $formModificationView = $this->createForm(FournisseurType::class, $fournisseurModif)->createView();
            }
        } else {
            $formModificationView = null;
        }

        return $this->render('fournisseur/index.html.twig', [
            'formRecherche' => $formRecherche->createView(),
            'formCreation' => $formCreation->createView(),
            'lesFournisseurs' => $lesFournisseurs,
            'formModification' => $formModificationView,
            'idFournisseurModif' => $id,
        ]);
    }

    /**
     * @Route("/fournisseur/ajouter", name="fournisseur_ajouter")
     *
     * @param Fournisseur|null $fournisseur
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FournisseurRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Fournisseur $fournisseur = null, Request $request, EntityManagerInterface $entityManager, FournisseurRepository $repository)
    {
        //  $fournisseur objet de la classe Fournisseur, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
        //  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
        //  $entityManager  pour la persistance des données

        // création d'un formulaire de type FournisseurType
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);

        // handleRequest met à jour le formulaire
        //  si le formulaire a été soumis, handleRequest renseigne les propriétés
        //      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // c'est le cas du retour du formulaire
            //         l'objet $fournisseur a été automatiquement "hydraté" par Doctrine
            dump($fournisseur);
            // dire à Doctrine que l'objet sera (éventuellement) persisté
            $entityManager->persist($fournisseur);
            // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
            $entityManager->flush();
            // ajouter un message flash de succès pour informer l'utilisateur
            $this->addFlash(
                'success',
                'Le fournisseur ' . $fournisseur->getNom() . ' a été ajoutée.'
            );
            // rediriger vers l'affichage des fournisseurs qui comprend le formulaire pour l"ajout d'une nouveau fournisseur
            return $this->redirectToRoute('fournisseur');

        } else {
            // affichage de la liste des fournisseurs avec le formulaire de création et ses erreurs
            // lire les fournisseurs
            $lesFournisseurs = $repository->findAll();
            // rendre la vue
            return $this->render('fournisseur/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesFournisseurs' => $lesFournisseurs,
                'formModification' => null,
                'idFournisseurModif' => null,
            ]);
        }
    }

    /**
     * @Route("/fournisseur/modifier/{id<\d+>}", name="fournisseur_modifier")
     *
     * @param Fournisseur|null $fournisseur
     * @param null $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FournisseurRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Fournisseur $fournisseur = null, $id = null, Request $request, EntityManagerInterface $entityManager, FournisseurRepository $repository)
    {
        //  Symfony 4 est capable de retrouver le fournisseur à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le fournisseur ' . $fournisseur->getNom() . ' a été modifiée.'
            );
            // rediriger vers l'affichage des fournisseurs qui comprend le formulaire pour l"ajout d'une nouveau fournisseur
            return $this->redirectToRoute('fournisseur');

        } else {
            // affichage de la liste des fournisseurs avec le formulaire de modification et ses erreurs
            // créer l'objet et le formulaire de création
            $fournisseur = new Fournisseur();
            $formCreation = $this->createForm(FournisseurType::class, $fournisseur);
            // lire les fournisseurs
            $lesFournisseurs = $repository->findAll();
            // rendre la vue
            return $this->render('fournisseur/index.html.twig', [
                'formCreation' => $formCreation->createView(),
                'lesFournisseurs' => $lesFournisseurs,
                'formModification' => $form->createView(),
                'idFournisseurModif' => $id,
            ]);
        }
    }

    /**
     * @Route("/fournisseur/supprimer/{id<\d+>}", name="fournisseur_supprimer")
     *
     * @param Fournisseur|null $fournisseur
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Fournisseur $fournisseur = null, Request $request, EntityManagerInterface $entityManager)
    {
        // vérifier le token
        if ($this->isCsrfTokenValid('action-item' . $fournisseur->getId(), $request->get('_token'))) {

            // supprimer le fournisseur
            $entityManager->remove($fournisseur);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le fournisseur ' . $fournisseur->getNom() . ' a été supprimé.'
            );
        }
        return $this->redirectToRoute('fournisseur');
    }
}
