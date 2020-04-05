<?php

namespace App\Repository;

use App\Entity\Fournisseur;
use App\Entity\FournisseurRecherche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Fournisseur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fournisseur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fournisseur[]    findAll()
 * @method Fournisseur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FournisseurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fournisseur::class);
    }

    /**
     * @param FournisseurRecherche $fournisseurRecherche
     * @return Query
     */
    public function findAllByCriteria(FournisseurRecherche $fournisseurRecherche): Query
    {
        // le "f" est un alias utilisé dans la requête
        $qb = $this->createQueryBuilder('f')
            ->orderBy('f.nom', 'ASC');

        if ($fournisseurRecherche->getNom()) {
            $qb->andWhere('f.nom LIKE :nom')
                ->setParameter('nom', $fournisseurRecherche->getNom() . '%');
        }

        if ($fournisseurRecherche->getDateMini()) {
            $qb->andWhere('f.dateRelation >= :dateMini')
                ->setParameter('dateMini', $fournisseurRecherche->getDateMini());
        }

        if ($fournisseurRecherche->getDateMaxi()) {
            $qb->andWhere('f.dateRelation < :dateMaxi')
                ->setParameter('dateMaxi', $fournisseurRecherche->getDateMaxi());
        }

        return $qb->getQuery();
    }
}
