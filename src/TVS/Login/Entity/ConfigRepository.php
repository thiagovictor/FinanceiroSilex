<?php

namespace TVS\Login\Entity;

use Doctrine\ORM\EntityRepository;

class ConfigRepository extends EntityRepository {

    public function findPagination($firstResult, $maxResults) {
        return $this->createQueryBuilder('c')
                        ->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->getQuery()
                        ->getResult();
    }

    public function getRows() {
        return $this->createQueryBuilder('c')
                        ->select('Count(c)')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    public function fatchPairs() {
        $entities = $this->findAll();
        $array = array();
        foreach ($entities as $entity) {
            $array[$entity->getId()] = $entity->getNome();
        }
        return $array;
    }

    public function findSearch($firstResult, $maxResults, $search, $field) {
        $query = $this->createQueryBuilder('c')
                ->where("c.{$field} like :search")
                ->setParameter('search', "%{$search}%")
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
                ->getQuery();
        return $query->getResult();
    }

    public function getRowsSearch($search, $field) {
        $query = $this->createQueryBuilder('c')
                ->select('Count(c)')
                ->where("c.{$field} like :search")
                ->setParameter('search', "%{$search}%")
                ->getQuery();
        return $query->getSingleScalarResult();
    }

}
