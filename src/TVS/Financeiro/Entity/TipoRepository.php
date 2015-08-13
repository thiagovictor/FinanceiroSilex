<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;

class TipoRepository extends EntityRepository {

    public function fatchPairs() {
        $tipos = $this->findAll();
        $array = array();
        foreach ($tipos as $tipo) {
            $array[$tipo->getId()] = $tipo->getDescricao();
        }
        return $array;
    }

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

    public function findById($id) {
        $config = $this->findOneById($id);
        if ($config) {
            return $config;
        }
        return false;
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
