<?php

namespace TVS\Financeiro\Service;

use Doctrine\ORM\EntityManager;
use TVS\Financeiro\Entity\Lancamento;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class LancamentoService extends AbstractService {

    public function __construct(EntityManager $em, Lancamento $object, Application $app) {
        parent::__construct($em, $app);
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

    public function ajustarDate($date,$remove = false) {
        $s = explode('/', $date);
        if($remove){
           return $s[2] . "-" . $s[1] . "-01"; 
        }
        return $s[2] . "-" . $s[1] . "-" . $s[0];
    }

    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());
        $data['status'] = (isset($data['status'])) ? $data['status'] : false;
        if ($data['status']) {
                $data['pagamento'] = new \DateTime("now");
        }
        $data["competencia"] = (isset($data['competencia'])) ? new \DateTime($this->ajustarMes($data["competencia"])): new \DateTime($this->ajustarDate($data["vencimento"],true));
        $data["vencimento"] = new \DateTime($this->ajustarDate($data["vencimento"]));
        if ("DESPESA" == $data["tipo"]) {
            $data["valor"] = "-" . $data["valor"];
        }
        $data['conta'] = $this->em->getReference('TVS\Financeiro\Entity\Conta', $data["conta"]);
        $data['favorecido'] = $this->em->getReference('TVS\Financeiro\Entity\Favorecido', $data["favorecido"]);
        $result = explode('_', $data['centrocusto']);
        $data['categoria'] = null;
        if (isset($result[1])) {
            $data['categoria'] = $this->em->getReference('TVS\Financeiro\Entity\Categoria', $result[1]);
        }
        $data['centrocusto'] = $this->em->getReference('TVS\Financeiro\Entity\Centrocusto', $result[0]);

        unset($data['option']);
        unset($data['tipo']);

        if(isset($data["parcelas"])){
            $data["idparcela"] = time();
            $this->createParcels($data);
            $data["descricao"] = $data["descricao"]."[1/{$data["parcelas"]}]";
        }
       
        return $data;
    }
    
    public function createParcels(array $array) {
        $parcelas = $array["parcelas"];
        if($parcelas <= 1){
            return true;
        }
        $descricao = $array["descricao"];
        for ($i = 2; $i <= $parcelas; $i++ ){
            $parcelamento = " [{$i}/{$parcelas}]";
            $array["descricao"] = $descricao."{$parcelamento}";
            $array["vencimento"] = (new \DateTime($array["vencimento"]->format("Y-m-d")))->add(new \DateInterval("P1M"));
            $array["competencia"] = (new \DateTime($array["vencimento"]->format("Y-m-d")))->add(new \DateInterval("P1M"));
            $registro = $this->hidrate(new Lancamento(),$array);
            if($registro){
               $this->em->persist($registro); 
            }
        }
        $this->em->flush();
        $this->setMessage("Parcelamento gerado para demais meses!");
    }

}
