<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class FavorecidoRepository extends EntityRepository{
    use \TVS\Base\Traits\RepositoryById;
    
    public function fatchPairs() {
        $user = (new Session())->get('user');
        $favorecidos = $this->findBy(array('user'=>$user->getId()),array());
        $array = array();
        foreach ($favorecidos as $favorecido ){
            $array[$favorecido->getId()] = $favorecido->getDescricao();
        }
        return $array;
    }
    public function selecao() {
        $user = (new Session())->get('user');
        $favorecidos = $this->findBy(array('user'=>$user->getId()),array());
        $array = array();
        $array[""] = "Selecione um item";
        foreach ($favorecidos as $favorecido ){
            $array[$favorecido->getId()] = $favorecido->getDescricao();
        }
        return $array;
    }
}
