<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class RecorrenteRepository extends EntityRepository {

    use \TVS\Base\Traits\Repository;

    public function findActives($user = false) {

        $base_date = (new Session())->get('baseDate');
        $query = $this->_em->createQuery(
                "select r from TVS\Financeiro\Entity\Recorrente as r
                where r.id not in (select l.idrecorrente from TVS\Financeiro\Entity\Lancamento as l where l.idrecorrente is not null and l.vencimento >= '{$base_date}-01' and l.vencimento <= '{$base_date}-31' and l.user = {$user->getId()} ) and r.status = 1 and r.user = {$user->getId()} and r.vencimento <= '{$base_date}-31'");
        $result = $query->getResult();
        //print($query->getSQL());
        return $result;
    }

    public function fatchPairs() {
        $user = (new Session())->get('user');
        $recorrentes = $this->findBy(array('user' => $user->getId()), array());
        $array = array();
        foreach ($recorrentes as $recorrente) {
            $array[$recorrente->getId()] = $recorrente->getDescricao();
        }
        return $array;
    }

    public function selecao() {
        $user = (new Session())->get('user');
        $recorrentes = $this->findBy(array('user' => $user->getId()), array());
        $array = array();
        $array[""] = "Selecione um item";
        foreach ($recorrentes as $recorrente) {
            $array[$recorrente->getId()] = $recorrente->getDescricao();
        }
        return $array;
    }

}
