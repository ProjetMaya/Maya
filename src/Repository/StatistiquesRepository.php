<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Recette;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class StatistiquesRepository
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
    * @return [] Retourne un tableau avec une ligne par catégorie
    */
    public function findAllCategoriesWithMaxAndMinPrices()
    {
		// ce n'est pas du SQL mais du DQL : Doctrine Query Language
        // il s'agit en fait d'une requête classique mais qui référence l'objet au lieu de la table
        $query = $this->entityManager->createQuery(
            'SELECT c.libelle, COUNT(p.id) AS nbProduits, MIN(p.prix) AS minPrix, MAX(p.prix) AS maxPrix
        FROM App\Entity\Categorie c
        JOIN c.produits p
		GROUP BY c.id
        ORDER BY c.libelle ASC'
        );

        // retourne un tableau d'objets de type Categorie
        return $query->getResult();
	}
	
	/**
    * @return [] Retourne un tableau avec une ligne par catégorie
    */
    public function findAllCategoriesWithPriceAverage()
    {
		// ce n'est pas du SQL mais du DQL : Doctrine Query Language
        // il s'agit en fait d'une requête classique mais qui référence l'objet au lieu de la table
        $query = $this->entityManager->createQuery(
            'SELECT c.libelle, COUNT(p.id) AS nbProduits, AVG(p.prix) AS moyennePrix
        FROM App\Entity\Categorie c
        JOIN c.produits p
		GROUP BY c.id
        ORDER BY nbProduits DESC'
        );

        // retourne un tableau d'objets de type Categorie
        return $query->getResult();
	}

	/**
    * @return [] Retourne un tableau avec une ligne par produit
    */
    public function findAllProductsWithinARecipe()
    {
		// ce n'est pas du SQL mais du DQL : Doctrine Query Language
        // il s'agit en fait d'une requête classique mais qui référence l'objet au lieu de la table
        $query = $this->entityManager->createQuery(
            'SELECT p.libelle, p.prix, p.bio, COUNT(r.id) AS nbRecettes
        FROM App\Entity\Produit p
        JOIN p.recettes r
		GROUP BY p.id
        ORDER BY p.libelle ASC'
        );

        // retourne un tableau d'objets de type Produit
        return $query->getResult();
	}
	
	/**
    * @return [] Retourne un tableau avec une ligne par recette
    */
    public function findAllRecipesWithTwoProducts()
    {
		// ce n'est pas du SQL mais du DQL : Doctrine Query Language
        // il s'agit en fait d'une requête classique mais qui référence l'objet au lieu de la table
        $query = $this->entityManager->createQuery(
            'SELECT r.nom, COUNT(p.id) AS nbProduits
        FROM App\Entity\Recette r
        JOIN r.produits p
		GROUP BY r.id
        ORDER BY r.nom ASC'
        );

        // retourne un tableau d'objets de type Recette
        return $query->getResult();
	}
}
