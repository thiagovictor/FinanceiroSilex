<?php

namespace TVS\Base\Traits;

trait Repository {

    public function findPagination($firstResult, $maxResults, $user = false) {
        $query = parent::createQueryBuilder('c');
        if ($user) {
            $query->where("c.user = :user")
                    ->setParameter('user', $user);
        }
        return $query->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->getQuery()
                        ->getResult();
    }

    public function getRows($user = false) {
        $query = parent::createQueryBuilder('c')
                ->select('Count(c)');
        if ($user) {
            $query->where("c.user = :user")
                    ->setParameter('user', $user);
        }
        return $query->getQuery()
                        ->getSingleScalarResult();
    }

    public function findSearch($firstResult, $maxResults, $search, $field, $user = false) {
        $query = parent::createQueryBuilder('c');
        if ($user) {
            $query->where("c.{$field} like :search and c.user = :user")
                    ->setParameter('search', "%{$search}%")
                    ->setParameter('user', $user);
        } else {
            $query->where("c.{$field} like :search")
                    ->setParameter('search', "%{$search}%");
        }
        return $query->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->getQuery()
                        ->getResult();
    }

    public function getRowsSearch($search, $field, $user = false) {
        $query = parent::createQueryBuilder('c')
                ->select('Count(c)');
        if ($user) {
            $query->where("c.{$field} like :search and c.user = :user")
                    ->setParameter('search', "%{$search}%")
                    ->setParameter('user', $user);
        } else {
            $query->where("c.{$field} like :search")
                    ->setParameter('search', "%{$search}%");
        }
        return $query->getQuery()
                     ->getSingleScalarResult();
    }

}
