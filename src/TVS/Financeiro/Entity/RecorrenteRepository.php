<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class RecorrenteRepository extends EntityRepository{
    use \TVS\Base\Traits\Repository;
    

    public function fatchPairs() {
        $user = (new Session())->get('user');
        $recorrentes = $this->findBy(array('user'=>$user->getId()),array());
        $array = array();
        foreach ($recorrentes as $recorrente ){
            $array[$recorrente->getId()] = $recorrente->getDescricao();
        }
        return $array;
    }
    public function selecao() {
        $user = (new Session())->get('user');
        $recorrentes = $this->findBy(array('user'=>$user->getId()),array());
        $array = array();
        $array[""] = "Selecione um item";
        foreach ($recorrentes as $recorrente ){
            $array[$recorrente->getId()] = $recorrente->getDescricao();
        }
        return $array;
    }
}
