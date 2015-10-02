<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Recorrente;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class RecorrenteService extends AbstractService {

    public function __construct(EntityManager $em, Recorrente $object, Application $app) {
        parent::__construct($em, $app);
        $this->object = $object;
        $this->entity = "TVS\Financeiro\Entity\Recorrente";
    }

    public function ajustarMes($date) {
        if ($date instanceof \DateTime) {
            return $date->format("Y-m") . "-01";
        }
        $s = explode('/', $date);
        return $s[1] . "-" . $s[0] . "-01";
    }

    public function ajustarDate($date, $remove = false) {
        $s = explode('/', $date);
        if ($remove) {
            return $s[2] . "-" . $s[1] . "-01";
        }
        return $s[2] . "-" . $s[1] . "-" . $s[0];
    }

    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        $data['status'] = (isset($data['status'])) ? $data['status'] : false;
        $data["vencimento"] = new \DateTime($this->ajustarDate($data["vencimento"]));
        if (isset($data["tipo"])) {
            if ("DESPESA" == $data["tipo"]) {
                $data["valor"] = "-" . $data["valor"];
            }
        }
        $data['conta'] = $this->em->getReference('TVS\Financeiro\Entity\Conta', $data["conta"]);
        if (isset($data['favorecido'])) {
            $data['favorecido'] = $this->em->getReference('TVS\Financeiro\Entity\Favorecido', $data["favorecido"]);
        }
        if (isset($data["centrocusto"])) {
            $result = explode('_', $data['centrocusto']);
            $data['categoria'] = null;
            if (isset($result[1])) {
                $data['categoria'] = $this->em->getReference('TVS\Financeiro\Entity\Categoria', $result[1]);
            }
            $data['centrocusto'] = $this->em->getReference('TVS\Financeiro\Entity\Centrocusto', $result[0]);
        }
        unset($data['option']);
        unset($data['tipo']);
        

        return $data;
    }

}
