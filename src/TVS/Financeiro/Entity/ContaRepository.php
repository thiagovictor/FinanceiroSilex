<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class ContaRepository extends EntityRepository{
    use \TVS\Base\Traits\Repository;
    
    public function fatchPairs() {
        $user = (new Session())->get('user');
        $contas = $this->findBy(array('user'=>$user->getId(),'ativo'=>true),array('descricao'=>'ASC'));
        $array = array();
        foreach ($contas as $conta ){
            $array[$conta->getId()] = $conta->getDescricao();
        }
        return $array;
    }
    public function selecao() {
        $user = (new Session())->get('user');
        $contas = $this->findBy(array('user'=>$user->getId()),array());
        $array = array();
        $array[""] = "Selecione um item";
        foreach ($contas as $conta ){
            $array[$conta->getId()] = $conta->getDescricao();
        }
        return $array;
    }
    
    public function infoAdditional($user) {
        /*$query = $this->_em->createQuery(
            "select c.descricao, c.saldo as saldoinicial, SUM(l.valor) as saldo
            FROM TVS\Financeiro\Entity\Conta c
            RIGHT JOIN TVS\Financeiro\Entity\Lancamento l WITH l.conta = c.id 
            where c.user = {$user->getId()} and l.status = 1 group by c.descricao");*/
        $query = $this->_em->createQuery(
            "select c.id, c.descricao, c.saldo as saldoinicial,(select sum(l.valor) from TVS\Financeiro\Entity\Lancamento as l where c.id = l.conta and l.status = 1 and l.user = {$user->getId()})as saldo
            FROM TVS\Financeiro\Entity\Conta as c
            where c.ativo = true and c.user = {$user->getId()}");  
            
    $result = $query->getResult();
    //var_dump($result);
    return $result;

    }
}