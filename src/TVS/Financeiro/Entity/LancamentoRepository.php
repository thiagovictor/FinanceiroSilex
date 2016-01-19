<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class LancamentoRepository extends EntityRepository{
    private $session;
    
    public function getSession() {
        if($this->session instanceof Session){
            return $this->session;
        }
        return $this->session = new Session();
    }
    
    public function findPagination($firstResult, $maxResults, $user = false) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c');
        if ($user) {
            $query->where("(c.competencia >= :date_start and c.competencia <= :date_end) and c.user = :user")
                    ->setParameters(array(
                    'date_start'=>$base_date."-01",
                    'date_end'=>$base_date."-31",
                    'user' => $user
                ));
        }
        return $query->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->orderBy('c.pagamento,c.vencimento', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

    public function getRows($user = false) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                ->select('Count(c)');
        if ($user) {
             $query->where("(c.competencia >= :date_start and c.competencia <= :date_end) and c.user = :user")
                    ->setParameters(array(
                    'date_start'=>$base_date."-01",
                    'date_end'=>$base_date."-31",
                    'user' => $user
                ));
        }
        return $query->getQuery()
                        ->getSingleScalarResult();
    }

    public function findSearch($firstResult, $maxResults, $search, $field, $user = false) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                    ->where("c.competencia >= :date_start and c.competencia <= :date_end and c.{$field} like :search and c.user = :user")
                    ->setParameters(array(
                        'search'=> "%{$search}%",
                        'date_start'=>$base_date."-01",
                        'date_end'=>$base_date."-31",
                        'user' => $user
                ));
       
        return $query->setFirstResult($firstResult)
                        ->setMaxResults($maxResults)
                        ->orderBy('c.pagamento,c.vencimento', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

    public function getRowsSearch($search, $field, $user = false) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                ->select('Count(c)')
                ->where("c.competencia >= :date_start and c.competencia <= :date_end and c.{$field} like :search and c.user = :user")
                    ->setParameters(array(
                        'search'=> "%{$search}%",
                        'date_start'=>$base_date."-01",
                        'date_end'=>$base_date."-31",
                        'user' => $user
                ));
        
        return $query->getQuery()
                     ->getSingleScalarResult();
    }
    public function receitas($user) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                ->select('SUM(c.valor)');
        if ($user) {
            $query->where("(c.competencia >= :date_start and c.competencia <= :date_end) and c.user = :user and c.valor > 0 and c.transf is null")
                    ->setParameters(array(
                    'date_start'=>$base_date."-01",
                    'date_end'=>$base_date."-31",
                    'user' => $user
                ));
        }
        $valor = $query->getQuery()
                     ->getSingleScalarResult();
        return ($valor)? $valor : '0';
    }
    public function despesas($user) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                ->select('SUM(c.valor)');
        if ($user) {
            $query->where("(c.competencia >= :date_start and c.competencia <= :date_end) and c.user = :user and c.valor < 0 and c.transf is null")
                    ->setParameters(array(
                    'date_start'=>$base_date."-01",
                    'date_end'=>$base_date."-31",
                    'user' => $user
                ));
        }
        $valor = $query->getQuery()
                     ->getSingleScalarResult();
        return ($valor)? $valor : '0';
    }
    public function infoAdditional($user = false) {
        
        $date = explode("-",$this->getSession()->get('baseDate'));
        $base_date = "{$date[1]}/{$date[0]}";
        return [
                    'despesa' => $this->despesas($user),
                    'receita' => $this->receitas($user),
                    'mesreferencia'=>$base_date,
                ];
    }
//    public function lancamentosGeralMes() {
//        $session = new Session();
//        $user = $session->get('user');
//        $baseDate = $session->get('baseDate');
//        $query = $this->createQueryBuilder('p')
//                ->where('((p.vencimento >= :dateBaseIni and p.vencimento <= :dateBaseFim) or (p.vencimento < :dateBaseIni and p.pagamento is null) or (p.pagamento >= :dateBaseIni and p.pagamento <= :dateBaseFim)) and p.user = :user')
//                ->setParameters(array(
//                    'dateBaseIni'=>$baseDate."-01",
//                    'dateBaseFim'=>$baseDate."-31",
//                    'user' => $user->getId()
//                ))
//                ->orderBy('p.pagamento,p.vencimento', 'ASC')
//                ->getQuery();
//        //\Zend\Debug\Debug::dump($query->getSQL());
//        //\Zend\Debug\Debug::dump($container->user->getId());
//        return $query->getResult();
//    }
}
