<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController
{
    /**
     * @Route("/recette", name="recette")
     */
    public function index(Request $request, RecetteRepository $repository, SessionInterface $session, PaginatorInterface $paginator)
    {
        $lesRecettes = $repository->findAll();

        return $this->render('recette/index.html.twig', [
            'formRecherche' => $lesRecettes,
            'lesRecettes' => $lesRecettes,
        ]);
    }

    /**
     * @Route("/recette/ajouter", name="recette_ajouter")
     */
    public function ajouter(Recette $recette = null, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
            $recette = $form->getData();
            // on met à jour la base de données 
            $entityManager->persist($recette);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le recette ' . $recette->getNom() . ' a été ajoutée.'
            );
            return $this->redirectToRoute('recette');
        } else {
            // cas où l'utilisateur a demandé l'ajout, onaffiche le formulaire d'ajout
            return $this->render('recette/ajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/recette/modifier/{id<\d+>}", name="recette_modifier")
     */
    public function modifier(Recette $recette = null, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire  a été soumis par l'utilisateur et est valide
            //pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le recette ' . $recette->getNom() . ' a été modifiée.'
            );

            return $this->redirectToRoute('recette');
        }
        // cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
        return $this->render('recette/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/recette/supprimer/{id<\d+>}", name="recette_supprimer")
     */
    public function supprimer(Recette $recette, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('action-item' . $recette->getId(), $request->get('_token'))) {
            $entityManager->remove($recette);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le recette ' . $recette->getNom() . ' a été supprimée.'
            );

            return $this->redirectToRoute('recette');
        }
    }
}
