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
        if(isset($data["option"])){
            if ($data["option"] == 'transferencia') {
                $data["status"] = true;
                $data["transf"] = time();
            }
        }
        if (isset($data["pagamento"])) {
            $data["pagamento"] = new \DateTime($this->ajustarDate($data["pagamento"]));
        }
        if ($data['status']) {
            $data['pagamento'] = new \DateTime("now");
        } else {
            $data['pagamento'] = null;
        }
        $data["competencia"] = (isset($data['competencia'])) ? new \DateTime($this->ajustarMes($data["competencia"])) : new \DateTime($this->ajustarDate($data["vencimento"], true));
        $data["vencimento"] = new \DateTime($this->ajustarDate($data["vencimento"]));

        if (isset($data["tipo"])) {
            if ("DESPESA" == $data["tipo"]) {
                $data["valor"] = "-" . $data["valor"];
            }
        }
        $data['conta'] = $this->em->getReference('TVS\Financeiro\Entity\Conta', $data["conta"]);
        if (isset($data["conta2"])) {
            $data['conta2'] = $this->em->getReference('TVS\Financeiro\Entity\Conta', $data["conta2"]);
        }

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


        if (isset($data["parcelas"])) {
            $data["idparcela"] = time();
            $this->createParcels($data);
            $data["descricao"] = $data["descricao"] . "[1/{$data["parcelas"]}]";
        }
        unset($data['arquivoComprovante']);
        unset($data['arquivoBoleto']);
        
        if (isset($data["conta2"])) {
            $data2 = $data;
            $data2["conta"] = $data2["conta2"];
            $data2["valor"] = "-" . $data2["valor"];
            unset($data2['conta2']);
            unset($data['conta2']);
            $this->createTransf($data2);
        }

        return $data;
    }

    public function createTransf(array $array) {
        $registro = $this->hidrate(new Lancamento(), $array);
        if ($registro) {
            $this->em->persist($registro);
        }
        $this->em->flush();
        $this->setMessage("Transfer&ecirc;ncia realizada!");
    }

    public function createParcels(array $array) {
        $parcelas = $array["parcelas"];
        if ($parcelas <= 1) {
            return true;
        }
        $descricao = $array["descricao"];
        for ($i = 2; $i <= $parcelas; $i++) {
            $parcelamento = " [{$i}/{$parcelas}]";
            $array["descricao"] = $descricao . "{$parcelamento}";
            $array["vencimento"] = (new \DateTime($array["vencimento"]->format("Y-m-d")))->add(new \DateInterval("P1M"));
            $array["competencia"] = (new \DateTime($array["vencimento"]->format("Y-m-d")))->add(new \DateInterval("P1M"));
            $registro = $this->hidrate(new Lancamento(), $array);
            if ($registro) {
                $this->em->persist($registro);
            }
        }
        $this->em->flush();
        $this->setMessage("Parcelamento gerado para demais meses!");
    }

    public static function checkDir($username) {
        $completePath = __DIR__ . "/../../../../data";

        if (!is_dir("{$completePath}/profile")) {
            mkdir("{$completePath}/profile");
        }

        if (!is_dir("{$completePath}/profile/{$username}")) {
            mkdir("{$completePath}/profile/{$username}");
        }

        if (!is_dir("{$completePath}/profile/{$username}/docs")) {
            mkdir("{$completePath}/profile/{$username}/docs");
        }
        return $completePath;
    }

    public static function uploadDocs($username, array $files = array()) {
        $completePath = LancamentoService::checkDir($username);
        if ('' != $files['tmp_name']['arquivoBoleto']) {
            $boletoPath = "/profile/{$username}/docs/boleto_" . time() . "." . substr($files['name']['arquivoBoleto'], -3);
            if (move_uploaded_file($files["tmp_name"]["arquivoBoleto"], $completePath . $boletoPath)) {
                $data["boleto"] = $boletoPath;
            }
        }
        if ('' != $files['tmp_name']['arquivoComprovante']) {
            $comprovantePath = "/profile/{$username}/docs/comprovante" . time() . "." . substr($files['name']['arquivoComprovante'], -3);
            if (move_uploaded_file($files["tmp_name"]["arquivoComprovante"], $completePath . $comprovantePath)) {
                $data["comprovante"] = $comprovantePath;
            }
        }

        if (isset($data)) {
            return $data;
        }

        return false;
    }

    publiC static function removeDocs($path) {
        $completePath = __DIR__ . "/../../../../data";
        if (unlink($completePath . $path)) {
            return true;
        }
        return false;
    }
    
    public function infoAdditional($user) {
        $repo = $this->em->getRepository($this->entity);
        return $repo->infoAdditional($user);
    }

}
