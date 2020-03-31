<?php


namespace App\Controller;


use App\Entity\Race;
use App\Form\RaceType;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends AbstractController
{
    /**
     * @Route("/race", name="race")
     * @Route("/race/demandermodification/{id<\d+>}", name="race_demandermodification")
     *
     * @param null $id
     * @param RaceRepository $repository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($id = null, RaceRepository $repository, Request $request)
    {
        // créer l'objet et le formulaire de création
        $race = new Race();
        $formCreation = $this->createForm(RaceType::class, $race);

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item' . $id, $request->get('_token'))) {
                $raceModif = $repository->find($id);   // la race à modifier
                $formModificationView = $this->createForm(RaceType::class, $raceModif)->createView();
            }
        } else {
            $formModificationView = null;
        }

        // lire les races
        $lesRaces = $repository->findAll();

        return $this->render('race/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesRaces' => $lesRaces,
            'formModification' => $formModificationView,
            'idRaceModif' => $id,
        ]);
    }

    /**
     * @Route("/race/ajouter", name="race_ajouter")
     *
     * @param Race|null $race
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param RaceRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Race $race = null, Request $request, EntityManagerInterface $entityManager, RaceRepository $repository)
    {
        //  $race objet de la classe Race, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
        //  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
        //  $entityManager  pour la persistance des données

        // création d'un formulaire de type RaceType
        $race = new Race();
        $form = $this->createForm(RaceType::class, $race);

        // handleRequest met à jour le formulaire
        //  si le formulaire a été soumis, handleRequest renseigne les propriétés
        //      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // c'est le cas du retour du formulaire
            //         l'objet $race a été automatiquement "hydraté" par Doctrine
            dump($race);
            // dire à Doctrine que l'objet sera (éventuellement) persisté
            $entityManager->persist($race);
            // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
            $entityManager->flush();
            // ajouter un message flash de succès pour informer l'utilisateur
            $this->addFlash(
                'success',
                'La race ' . $race->getIntitule() . ' a été ajoutée.'
            );
            // rediriger vers l'affichage des races qui comprend le formulaire pour l"ajout d'une nouvelle race
            return $this->redirectToRoute('race');

        } else {
            // affichage de la liste des races avec le formulaire de création et ses erreurs
            // lire les races
            $lesRaces = $repository->findAll();
            // rendre la vue
            return $this->render('race/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesRaces' => $lesRaces,
                'formModification' => null,
                'idRaceModif' => null,
            ]);
        }
    }

    /**
     * @Route("/race/modifier/{id<\d+>}", name="race_modifier")
     *
     * @param Race|null $race
     * @param null $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param RaceRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Race $race = null, $id = null, Request $request, EntityManagerInterface $entityManager, RaceRepository $repository)
    {
        //  Symfony 4 est capable de retrouver la race à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La race ' . $race->getIntitule() . ' a été modifiée.'
            );
            // rediriger vers l'affichage des races qui comprend le formulaire pour l"ajout d'une nouvelle race
            return $this->redirectToRoute('race');

        } else {
            // affichage de la liste des races avec le formulaire de modification et ses erreurs
            // créer l'objet et le formulaire de création
            $race = new Race();
            $formCreation = $this->createForm(RaceType::class, $race);
            // lire les races
            $lesRaces = $repository->findAll();
            // rendre la vue
            return $this->render('race/index.html.twig', [
                'formCreation' => $formCreation->createView(),
                'lesRaces' => $lesRaces,
                'formModification' => $form->createView(),
                'idRaceModif' => $id,
            ]);
        }
    }

    /**
     * @Route("/race/supprimer/{id<\d+>}", name="race_supprimer")
     *
     * @param Race|null $race
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Race $race = null, Request $request, EntityManagerInterface $entityManager)
    {
        // vérifier le token
        if ($this->isCsrfTokenValid('action-item' . $race->getId(), $request->get('_token'))) {
            if ($race->getAnimaux()->count() > 0) {
                $this->addFlash(
                    'error',
                    'Il existe des animaux dans la race ' . $race->getIntitule() . ', elle ne peut pas être supprimée.'
                );
                return $this->redirectToRoute('race');
            }
            // supprimer la race
            $entityManager->remove($race);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La race ' . $race->getIntitule() . ' a été supprimée.'
            );
        }
        return $this->redirectToRoute('race');
    }
}
