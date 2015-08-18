<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class CentrocustoRepository extends EntityRepository {

    use \TVS\Base\Traits\Repository;

    public function fatchPairs() {
        $user = (new Session())->get('user');
        $entities = $this->findBy(array('user' => $user->getId()), array());
        $array = array();
        foreach ($entities as $entity) {
            $array[$entity->getId()] = $entity->getDescricao();
        }
        return $array;
    }

    public function selecao() {
        $user = (new Session())->get('user');
        $entities = $this->findBy(array('user' => $user->getId()), array());
        $array = array();
        $array[""] = "Selecione um item";
        foreach ($entities as $entity) {
            $array[$entity->getId()] = $entity->getDescricao();
        }
        return $array;
    }

}
