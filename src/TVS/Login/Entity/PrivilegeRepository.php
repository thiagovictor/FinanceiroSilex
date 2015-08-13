<?php

namespace TVS\Login\Entity;

use Doctrine\ORM\EntityRepository;

class PrivilegeRepository extends EntityRepository {

    public function findPagination($firstResult, $maxResults) {
        return $this->createQueryBuilder('c')
                        ->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->getQuery()
                        ->getResult();
    }

    public function findSearch($firstResult, $maxResults, $search, $field) {
        $query = $this->createQueryBuilder('c')
                ->innerJoin('TVS\Login\Entity\Route', 'g', 'WITH', "g.id = c.{$field}")
                ->where("g.{$field} like :search")
                ->setParameter('search', "%{$search}%")
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
                ->getQuery();
//                var_dump($query->getSQL());
//                exit();
        return $query->getResult();
    }

    public function getRowsSearch($search, $field) {
        $query = $this->createQueryBuilder('c')
                ->select('Count(c)')
                ->innerJoin('TVS\Login\Entity\Route', 'g', 'WITH', "g.id = c.{$field}")
                ->where("g.{$field} like :search")
                ->setParameter('search', "%{$search}%")
                ->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getRows() {
        return $this->createQueryBuilder('c')
                        ->select('Count(c)')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

}
