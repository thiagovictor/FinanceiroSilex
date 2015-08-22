<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class ContaRepository extends EntityRepository{
    use \TVS\Base\Traits\Repository;
    
    public function fatchPairs() {
        $user = (new Session())->get('user');
        $contas = $this->findBy(array('user'=>$user->getId()),array());
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
}