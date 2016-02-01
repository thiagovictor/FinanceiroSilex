<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class CartaoRepository extends EntityRepository {
    
    use \TVS\Base\Traits\Repository;

    public function fatchPairs() {
        $user = (new Session())->get('user');
        $cartoes = $this->findBy(array('user' => $user->getId()), array());
        $array = array();
        foreach ($cartoes as $cartao) {
            $array[$cartao->getId()] = $cartao->getDescricao();
        }
        return $array;
    }

    public function selecao() {
        $user = (new Session())->get('user');
        $cartoes = $this->findBy(array('user' => $user->getId()), array());
        $array = array();
        $array["0"] = "Selecione o cartao";
        foreach ($cartoes as $cartao) {
            $array[$cartao->getId()] = $cartao->getDescricao();
        }
        return $array;
    }
    
    public function findPagination($firstResult, $maxResults, $user = false) {
        $base_date = (new Session())->get('baseDate');
        $format = new \DateTime($base_date."-01");
        $query = $this->_em->createQuery(
                "select c.id, c.descricao,c.vencimento,'{$format->format("m/Y")}' as competencia, (select sum(l.valor) from TVS\Financeiro\Entity\Lancamento as l where l.user = {$user->getId()} and l.cartao = c.id and l.competencia = '{$base_date}-01' ) as total  from TVS\Financeiro\Entity\Cartao as c
                where c.user = {$user->getId()}");
        return $query->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        //->getQuery()
                        ->getResult();
    }
    
    public function findSearch($firstResult, $maxResults, $search, $field, $user = false) {
        $base_date = (new Session())->get('baseDate');
        $format = new \DateTime($base_date."-01");
        $query = $this->_em->createQuery(
                "select c.id, c.descricao,c.vencimento,'{$format->format("m/Y")}' as competencia, (select sum(l.valor) from TVS\Financeiro\Entity\Lancamento as l where l.user = {$user->getId()} and l.cartao = c.id and l.competencia = '{$base_date}-01' ) as total  from TVS\Financeiro\Entity\Cartao as c
                where c.user = {$user->getId()} and c.{$field} like '%{$search}%' ");
        return $query->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        //->getQuery()
                        ->getResult();
    }

}
