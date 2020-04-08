<?php
namespace App\Controller;

use App\Repository\StatistiquesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
	 /**
     * @Route("/statistiques/categoriePrixMaxEtMin", name="categoriePrixMaxEtMin")
     * @param StatistiquesRepository $repository
     */
    public function categoriePrixMaxEtMin(StatistiquesRepository $repository)
    {
        $lesCategories = $repository->findAllCategoriesWithMaxAndMinPrices();
		//rendre la vue
		return $this->render('statistiques/categoriePrixMaxEtMin.html.twig', ['lesCategories' => $lesCategories,]);
    }


    /**
     * @Route("/statistiques/categorieMoyennePrix", name="categorieMoyennePrix")
     * @param StatistiquesRepository $repository
     */
    public function categorieMoyennePrix(StatistiquesRepository $repository)
    {
        $lesCategories = $repository->findAllCategoriesWithPriceAverage();
		//rendre la vue
		return $this->render('statistiques/categorieMoyennePrix.html.twig', ['lesCategories' => $lesCategories,]);
    }

	/**
     * @Route("/statistiques/produitSiRecette", name="produitSiRecette")
     * @param StatistiquesRepository $repository
     */
    public function produitSiRecette(StatistiquesRepository $repository)
    {
        $lesProduits = $repository->findAllProductsWithinARecipe();
		//rendre la vue
		return $this->render('statistiques/produitSiRecette.html.twig', ['lesProduits' => $lesProduits,]);
    }

	/**
     * @Route("/statistiques/recetteSiDeuxProduits", name="recetteSiDeuxProduits")
     * @param StatistiquesRepository $repository
     */
    public function recetteSiDeuxProduits(StatistiquesRepository $repository)
    {
        $lesRecettes = $repository->findAllRecipesWithTwoProducts();
		//rendre la vue
		return $this->render('statistiques/recetteSiDeuxProduits.html.twig', ['lesRecettes' => $lesRecettes,]);
    }
}