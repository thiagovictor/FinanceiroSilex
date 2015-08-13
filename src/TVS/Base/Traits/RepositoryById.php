<?php

namespace TVS\Base\Traits;

use Symfony\Component\HttpFoundation\Session\Session;

trait RepositoryById {

    public function findPagination($firstResult, $maxResults) {
        $user = (new Session())->get('user');
        return parent::createQueryBuilder('c')
                        ->where("c.user = :user")
                        ->setParameter('user', $user)
                        ->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->getQuery()
                        ->getResult();
    }

    public function getRows() {
        $user = (new Session())->get('user');
        return parent::createQueryBuilder('c')
                        ->select('Count(c)')
                        ->where("c.user = :user")
                        ->setParameter('user', $user)
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
        $user = (new Session())->get('user');
        $query = parent::createQueryBuilder('c')
                ->where("c.{$field} like :search and c.user = :user")
                ->setParameter('search', "%{$search}%")
                ->setParameter('user', $user)
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
                ->getQuery();
        return $query->getResult();
    }

    public function getRowsSearch($search, $field) {
        $user = (new Session())->get('user');
        $query = parent::createQueryBuilder('c')
                ->select('Count(c)')
                ->where("c.{$field} like :search and c.user = :user")
                ->setParameter('search', "%{$search}%")
                ->setParameter('user', $user)
                ->getQuery();
        return $query->getSingleScalarResult();
    }

}
