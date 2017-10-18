<?php

namespace AppBundle\Repository;

/**
 * ThemeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ThemeRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllThemes()
    {
        //Instanciation de QueryBuilder($qb)
        $qb = $this->createQueryBuilder('t');

        //Ordre SQL sur les ENTITY. UPPER permet de mettre en MAJ le résultat
        //$qb->select("t.id, UPPER(t.name) as name");

        //Ordre SQL sur les ENTITY (ici un SELECT name COUNT) avec une jointure sur ENTITY Post. Le tout groupé
        //par les id des différents thèmes.
        $qb->select("t.name, count(p) as numberOfPosts, t.slug")
            ->innerJoin("t.posts", "p")
            ->groupBy("t.slug");

        //DQL chaine de caractère qui ressemble à du SQL
        dump($qb->getDQL());

        //On retourne une méthode getQuery qui retourne la requête instanciée précédemment
        return $qb->getQuery();
    }
}
