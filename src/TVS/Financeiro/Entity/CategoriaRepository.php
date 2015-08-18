<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoriaRepository extends EntityRepository {

    use \TVS\Base\Traits\Repository;

    public function fatchPairs() {
        $user = (new Session())->get('user');
        $categorias = $this->findBy(array('user' => $user->getId()), array());
        $array = array();
        foreach ($categorias as $categoria) {
            $array[$categoria->getId()] = $categoria->getDescricao();
        }
        return $array;
    }

}
