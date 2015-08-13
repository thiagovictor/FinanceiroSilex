<?php

namespace TVS\Login\Service;

use Doctrine\ORM\EntityManager;
use TVS\Login\Entity\Config;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class ConfigService extends AbstractService {

    public function __construct(EntityManager $em, Config $config, Application $app) {
        parent::__construct($em,$app);
        $this->object = $config;
        $this->entity = "TVS\Login\Entity\Config";
    }
    
    public function findConfig($config) {
        $repo = $this->em->getRepository($this->entity);
        $object = $repo->findOneBy(['nome'=>$config]);
        if ($object) {
            return $object;
        }
        return false;
    }
    
    
    public function ajustaData(array $data = array()) {
        return $data;
    }
}
