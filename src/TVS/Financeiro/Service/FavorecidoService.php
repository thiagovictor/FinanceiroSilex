<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Favorecido;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class FavorecidoService extends AbstractService {

    public function __construct(EntityManager $em, Favorecido $object, Application $app) {
        parent::__construct($em,$app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Favorecido";
    }
    
    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        return $data;
    }
}
