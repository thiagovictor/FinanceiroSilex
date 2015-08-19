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

}
