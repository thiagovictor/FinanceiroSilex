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
    
    public function ajustarMes($date) {
        if ($date instanceof \DateTime) {
            return $date->format("Y-m") . "-01";
        }
        $s = explode('/', $date);
        return $s[1] . "-" . $s[0] . "-01";
    }
    
    public function ajustarDate($date) {
        $s = explode('/', $date);
        return $s[2] . "-" . $s[1] . "-" . $s[0];
    }
    
    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        if($data['status']){
           $data['pagamento'] = new \DateTime("now");
        }
        $data["competencia"] = new \DateTime($this->ajustarMes($data["competencia"]));
        $data["vencimento"] = new \DateTime($this->ajustarDate($data["vencimento"]));
        if ("DESPESA" == $data["tipo"]) {
            $data["valor"] = "-" . $data["valor"];
        }
        $data['conta'] = $this->em->getReference('TVS\Financeiro\Entity\Conta', $data["conta"]);
        $data['favorecido'] = $this->em->getReference('TVS\Financeiro\Entity\Favorecido', $data["favorecido"]);
        $result = explode('_',$data['centrocusto']);
        $data['categoria'] = null;
        if(isset($result[1])){
            $data['categoria'] = $this->em->getReference('TVS\Financeiro\Entity\Categoria', $result[1]);
        }
        $data['centrocusto'] = $this->em->getReference('TVS\Financeiro\Entity\Centrocusto',$result[0]);
        
        unset($data['option']);
        unset($data['tipo']);
//        foreach ($data as $key => $value) {
//            if(!$value){
//                unset($data[$key]);
//            }
//        }
        
        
//        var_dump($data);
//        exit();
        
        return $data;
    }
}
