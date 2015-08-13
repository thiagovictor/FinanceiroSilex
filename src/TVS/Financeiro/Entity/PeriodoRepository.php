<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;

class PeriodoRepository extends EntityRepository {
    
    use \TVS\Base\Traits\Repository;
    
    public function fatchPairs() {
        $periodos = $this->findAll();
        $array = array();
        foreach ($periodos as $periodo ){
            $array[$periodo->getId()] = $periodo->getDescricao();
        }
        return $array;
    }
}
