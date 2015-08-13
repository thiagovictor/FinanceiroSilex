<?php

namespace TVS\Login\Service;

use Doctrine\ORM\EntityManager;
use TVS\Login\Entity\Route;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class RouteService extends AbstractService {

    public function __construct(EntityManager $em, Route $rota, Application $app) {
        parent::__construct($em,$app);
        $this->object = $rota;
        $this->entity = "TVS\Login\Entity\Route";
    }

    public function ajustaData(array $data = array()) {
        if (!empty($data["menu"])) {
            $repo = $this->em->getRepository("TVS\Login\Entity\Menu");
            $data["menu"] = $repo->findById($data["menu"]);
        }
        return $data;
    }

}
