<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPostsGroupedByYear()
    {
        //Instanciation de $qb et affectation d'un alias
        $qb = $this->createQueryBuilder("p");

        //Ordre SQL
        $qb->select("YEAR(p.createdAt) as yearPublished,
                            COUNT(p.id) as numberOfPosts")
            ->groupBy("yearPublished");

        return $qb->getQuery()->getArrayResult();
    }

    public function getPostsByYear($year)
    {
        //Instanciation de $qb et affectation d'un alias
        $qb = $this->createQueryBuilder("p");

        //Ordre SQL de style doctrine
        //SELECT de tout les champs de la table Post ou l'année est égale au paramètre de l'année
        $qb->select("p")
            ->where("YEAR(p.createdAt)= :year") //Utilisation de OROCRM qui récupère le DateTime de createdat
            ->setParameter("year", $year);

        return $qb->getQuery()->getResult();
    }
}