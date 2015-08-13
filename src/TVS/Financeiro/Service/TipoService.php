<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Tipo;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class TipoService extends AbstractService {

    public function __construct(EntityManager $em, Tipo $object, Application $app) {
        parent::__construct($em,$app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Tipo";
    }
}
