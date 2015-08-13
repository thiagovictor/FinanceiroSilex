<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Periodo;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class PeriodoService extends AbstractService {

    public function __construct(EntityManager $em, Periodo $object, Application $app) {
        parent::__construct($em,$app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Periodo";
    }
}
