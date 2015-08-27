<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Lancamento;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class LancamentoService extends AbstractService {

    public function __construct(EntityManager $em, Lancamento $object, Application $app) {
        parent::__construct($em,$app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Lancamento";
    }
    
    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        
        var_dump($data);
        exit();
        
        return $data;
    }
}
