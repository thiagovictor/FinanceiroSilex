<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;

class TipoRepository extends EntityRepository {

    use \TVS\Base\Traits\Repository;

    public function fatchPairs() {
        $tipos = $this->findAll();
        $array = array();
        foreach ($tipos as $tipo) {
            $array[$tipo->getId()] = $tipo->getDescricao();
        }
        return $array;
    }

}
