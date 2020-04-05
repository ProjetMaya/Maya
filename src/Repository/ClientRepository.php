<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\ClientRecherche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @return Query
     */
    public function findAllByCriteria(ClientRecherche $clientRecherche): Query
    {
        // le "c" est un alias utilisé dans la requête
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC');

        if ($clientRecherche->getNom()) {
            $qb->andWhere('c.nom LIKE :nom')
                ->setParameter('nom', $clientRecherche->getNom().'%');
        }
		
		if ($clientRecherche->getPrenom()) {
            $qb->andWhere('c.prenom LIKE :prenom')
                ->setParameter('prenom', $clientRecherche->getPrenom().'%');
        }

        if ($clientRecherche->getDateMini()) {
            $qb->andWhere('c.datePremierContact >= :dateMini')
                ->setParameter('dateMini', $clientRecherche->getDateMini());
        }

        if ($clientRecherche->getDateMaxi()) {
            $qb->andWhere('c.datePremierContact < :dateMaxi')
                ->setParameter('dateMaxi', $clientRecherche->getDateMaxi());
        }
		return $qb->getQuery();
    }
	
	 /**
     * @return Query
     */
    public function findAllOrderByNom(): Query
    {
        // le "c" est un alias utilisé dans la requête
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC');

        // retourne un query
        return $qb->getQuery();
    }
}
