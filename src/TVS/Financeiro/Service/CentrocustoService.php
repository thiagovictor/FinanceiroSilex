<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Centrocusto;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class CentrocustoService extends AbstractService {

    public function __construct(EntityManager $em, Centrocusto $object, Application $app) {
        parent::__construct($em,$app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Centrocusto";
    }
    
    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        return $data;
    }
}
