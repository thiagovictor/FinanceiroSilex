<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class LancamentoRepository extends EntityRepository {

    private $session;

    public function getSession() {
        if ($this->session instanceof Session) {
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
                        'date_start' => $base_date . "-01",
                        'date_end' => $base_date . "-31",
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
                        'date_start' => $base_date . "-01",
                        'date_end' => $base_date . "-31",
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
            'search' => "%{$search}%",
            'date_start' => $base_date . "-01",
            'date_end' => $base_date . "-31",
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
            'search' => "%{$search}%",
            'date_start' => $base_date . "-01",
            'date_end' => $base_date . "-31",
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
                        'date_start' => $base_date . "-01",
                        'date_end' => $base_date . "-31",
                        'user' => $user
            ));
        }
        $valor = $query->getQuery()
                ->getSingleScalarResult();
        return ($valor) ? $valor : '0';
    }

    public function despesas($user) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                ->select('SUM(c.valor)');
        if ($user) {
            $query->where("(c.competencia >= :date_start and c.competencia <= :date_end) and c.user = :user and c.valor < 0 and c.transf is null")
                    ->setParameters(array(
                        'date_start' => $base_date . "-01",
                        'date_end' => $base_date . "-31",
                        'user' => $user
            ));
        }
        $valor = $query->getQuery()
                ->getSingleScalarResult();
        return ($valor) ? $valor : '0';
    }
    
    public function despesasbycusto($user) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                ->select('SUM(c.valor) as total, cc.descricao');
        $query->join('c.centrocusto', 'cc');
        if ($user) {
            $query->where("(c.competencia >= :date_start and c.competencia <= :date_end) and c.user = :user and c.valor < 0 and c.transf is null")
                    ->setParameters(array(
                        'date_start' => $base_date . "-01",
                        'date_end' => $base_date . "-31",
                        'user' => $user
            ));
        }
        $query->groupBy('c.centrocusto');
        $valor = $query->getQuery()
                ->getResult();
        return $valor;
    }
    
    public function receitasbycusto($user) {
        $session = $this->getSession();
        $base_date = $session->get('baseDate');
        $query = $this->createQueryBuilder('c')
                ->select('SUM(c.valor) as total, cc.descricao');
        $query->join('c.centrocusto', 'cc');
        if ($user) {
            $query->where("(c.competencia >= :date_start and c.competencia <= :date_end) and c.user = :user and c.valor > 0 and c.transf is null")
                    ->setParameters(array(
                        'date_start' => $base_date . "-01",
                        'date_end' => $base_date . "-31",
                        'user' => $user
            ));
        }
        $valor = $query->getQuery()
                ->getResult();
        return $valor;
    }

    public function despesasCusto($user,$base_date,$ccusto = null) {
        $query = $this->createQueryBuilder('c')
                ->select('SUM(c.valor)');
        if ($user) {
            $query->where("c.competencia = :date_start and c.user = :user and c.valor < 0 and c.transf is null and c.centrocusto = :ccusto")
                    ->setParameters(array(
                        'ccusto'=> $ccusto,
                        'date_start' => $base_date,
                        'user' => $user
            ));
        }
//        echo $query->getQuery()->getSQL();
//        var_dump($ccusto,$base_date,$user);
//        exit();
        $valor = $query->getQuery()
                ->getSingleScalarResult();
        return ($valor) ? (float) $valor*-1 : 0;
    }

    public function infoAdditional($user = false) {

        $date = explode("-", $this->getSession()->get('baseDate'));
        $base_date = "{$date[1]}/{$date[0]}";
        return [
            'despesa' => $this->despesas($user),
            'receita' => $this->receitas($user),
            'mesreferencia' => $base_date,
            'despesasbycusto' => $this->despesasbycusto($user),
            'receitasbycusto' => $this->receitasbycusto($user),
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

    public function pagamentoFatura($id_cartao, $user) {
        $base_date = (new Session())->get('baseDate');
        $competencia = new \DateTime($base_date . "-01");
        $pagamento = new \DateTime("now");
        $query = $this->_em->createQuery(
                "update TVS\Financeiro\Entity\lancamento as l set l.status = 1, l.pagamento = '{$pagamento->format("Y-m-d")}'
                where l.user = {$user->getId()} and l.cartao = {$id_cartao} and l.competencia = '{$competencia->format("Y-m-d")}' and l.status = 0");
        $result = $query->getResult();

        return $result;
    }

    public function historicoSaldos($user) {
        $query = $this->_em->createQuery(
                "select distinct l.competencia
                    ,(SELECT sum(d.valor) FROM TVS\Financeiro\Entity\lancamento as d where d.status = 1 and d.valor < 0 and d.transf is null and d.competencia = l.competencia and d.user = l.user) as despesa
                    ,(SELECT sum(r.valor) FROM TVS\Financeiro\Entity\lancamento as r where r.status = 1 and r.valor > 0 and r.transf is null and r.competencia = l.competencia and r.user = l.user) as receita
                from TVS\Financeiro\Entity\lancamento as l where l.status = 1 and l.user = {$user->getId()} order by l.competencia ASC");
        $result = $query->getResult();
        return $result;
    }

    public function relatorioCustom($param) {
        //var_dump($param);
        //exit();
        $query = $this->createQueryBuilder('l')
                ->where($param['query'])
                ->setParameters($param['param']);
        return $query->getQuery()
                        ->getResult();
    }

}
