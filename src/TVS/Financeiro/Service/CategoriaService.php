<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Categoria;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class CategoriaService extends AbstractService {

    public function __construct(EntityManager $em, Categoria $object, Application $app) {
        parent::__construct($em,$app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Categoria";
    }
    
    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        
        if (!empty($data["centrocusto"])) {
            $data['centrocusto'] = $this->em->getReference('TVS\Financeiro\Entity\Centrocusto', $data['centrocusto']);
        }
        return $data;
    }
}
