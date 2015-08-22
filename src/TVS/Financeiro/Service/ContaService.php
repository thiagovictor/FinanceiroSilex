<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Conta;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class ContaService extends AbstractService {

    public function __construct(EntityManager $em, Conta $object, Application $app) {
        parent::__construct($em,$app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Conta";
    }
    
    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        return $data;
    }
}
