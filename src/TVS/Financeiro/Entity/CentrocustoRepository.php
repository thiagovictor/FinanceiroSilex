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
        $query = $this->_em->createQuery('SELECT u.descricao as centrocusto,u.id, c.descricao as categoria, c.id as idcategoria FROM TVS\Financeiro\Entity\Centrocusto u LEFT JOIN TVS\Financeiro\Entity\Categoria c WITH u.id = c.centrocusto where u.user = :user ORDER BY u.descricao,c.descricao');
        $query->setParameter('user', $user->getId());
        $entities = $query->getResult();
        $array = array();
        $id_ = 0;
        foreach ($entities as $entity) {
            $id = $entity['id'];
            $descricao = $entity['centrocusto'];
            if($id_ != $id){
               $array[$id] = $descricao; 
               $id_ = $id;
            }
            
            if (null != $entity['idcategoria'] and null != $entity['categoria'] ){
               $id .= "_".$entity['idcategoria'];
               $descricao .= " \ ".$entity['categoria']; 
            }
            
            $array[$id] = $descricao;
        }
        return $array;
    }

}
