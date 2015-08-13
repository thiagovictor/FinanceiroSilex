<?php

namespace TVS\Base\Traits;

trait Repository {

    public function findPagination($firstResult, $maxResults) {
        return parent::createQueryBuilder('c')
                        ->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->getQuery()
                        ->getResult();
    }

    public function getRows() {
        return parent::createQueryBuilder('c')
                        ->select('Count(c)')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    public function findById($id) {
        $config = parent::findOneById($id);
        if ($config) {
            return $config;
        }
        return false;
    }

    public function findSearch($firstResult, $maxResults, $search, $field) {
        $query = parent::createQueryBuilder('c')
                ->where("c.{$field} like :search")
                ->setParameter('search', "%{$search}%")
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
                ->getQuery();
        return $query->getResult();
    }

    public function getRowsSearch($search, $field) {
        $query = parent::createQueryBuilder('c')
                ->select('Count(c)')
                ->where("c.{$field} like :search")
                ->setParameter('search', "%{$search}%")
                ->getQuery();
        return $query->getSingleScalarResult();
    }

}
