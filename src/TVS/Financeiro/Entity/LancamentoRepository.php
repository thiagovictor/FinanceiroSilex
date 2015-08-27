<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\EntityRepository;

class LancamentoRepository extends EntityRepository{
    
    use \TVS\Base\Traits\Repository;
    
    public function lancamentosGeralMes() {
        $session = new Session();
        $user = $session->get('user');
        $baseDate = $session->get('baseDate');
        $query = $this->createQueryBuilder('p')
                ->where('((p.vencimento >= :dateBaseIni and p.vencimento <= :dateBaseFim) or (p.vencimento < :dateBaseIni and p.pagamento is null) or (p.pagamento >= :dateBaseIni and p.pagamento <= :dateBaseFim)) and p.user = :user')
                ->setParameters(array(
                    'dateBaseIni'=>$baseDate."-01",
                    'dateBaseFim'=>$baseDate."-31",
                    'user' => $user->getId()
                ))
                ->orderBy('p.pagamento,p.vencimento', 'ASC')
                ->getQuery();
        //\Zend\Debug\Debug::dump($query->getSQL());
        //\Zend\Debug\Debug::dump($container->user->getId());
        return $query->getResult();
    }
}
