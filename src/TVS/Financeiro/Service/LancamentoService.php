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

    public function insertRecorrentes($array) {
        $controle = false;
        foreach ($array as $object) {
            $data = $object->toArray();
            $data['idrecorrente'] = $data["id"];
            $data['status'] = 0;
            unset($data['id']);
            if ($data['periodo'] != 1) {
                $periodo = $this->em->getReference('TVS\Financeiro\Entity\Periodo', $data['periodo']);
                if (!$this->verificaIntervaloRecorrencia($object->getVencimento()->format('Y-m-d'), $periodo->getIncremento())) {
                    continue;
                }
            }
            unset($data["periodo"]);
            $data['vencimento'] = (new \DateTime($this->app['session']->get('baseDate') . "-{$object->getVencimento()->format('d')}"))->format("d/m/Y");
            $registro = $this->hidrate(new Lancamento(), $this->ajustaData($data));
            if ($registro) {
                $controle = true;
                $this->em->persist($registro);
            }
        }
        if ($controle) {
            $this->em->flush();
        }
    }

    public function verificaIntervaloRecorrencia($vencimentoInicial, $incremento) {
        $base = new \DateTime($this->app['session']->get('baseDate') . "-01");
        $inicio = new \DateTime($vencimentoInicial);
        $intervalo = new \DateInterval("P{$incremento}M");
        while ($base >= $inicio) {
            $inicio->add($intervalo);
            //echo "{$inicio->format('m/Y')} == {$base->format('m/Y')}<br>";
            if ($inicio->format('m/Y') == $base->format('m/Y')) {
                return true;
            }
        }
        return false;
    }

    public function findPagination($firstResult, $maxResults, $user) {
        $repoRecorrente = $this->em->getRepository("TVS\Financeiro\Entity\Recorrente");
        $this->insertRecorrentes($repoRecorrente->findActives($user));

        $repo = $this->em->getRepository($this->entity);
        return $repo->findPagination($firstResult, $maxResults, $user);
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
            if (sizeof($s) == 2) {
                return $s[1] . "-" . $s[0] . "-01";
            }
            return $s[2] . "-" . $s[1] . "-01";
        }
        return $s[2] . "-" . $s[1] . "-" . $s[0];
    }

    public function ajustaData(array $data = array()) {
        $user = $this->app['session']->get('user');
        $data['user'] = $this->em->getReference('TVS\Login\Entity\User', $user->getId());

        if (!isset($data['status'])) {
            $data['status'] = 0;
        }

        $data['status'] = ($data['status']) ? 1 : 0;

        if (isset($data["option"])) {
            if ($data["option"] == 'transferencia') {
                $data["status"] = 1;
                $data["transf"] = time();
                $data["pagamento"] = $data["vencimento"];
            }
        }

        if (!$data['status']) {
            $data['pagamento'] = null;
        } else {
            if (isset($data["pagamento"])) {
                $data['pagamento'] = new \DateTime($this->ajustarDate($data["pagamento"]));
            } else {
                $data['pagamento'] = new \DateTime("now");
            }
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

        if (isset($data["cartao"])) {
            $data['cartao'] = $this->em->getReference('TVS\Financeiro\Entity\Cartao', $data["cartao"]);
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
        var_dump($data);exit();
        return $data;
    }

    public function relatorioCustom($data) {
        $user = $this->app['session']->get('user');
        $data['user'] = $user->getId();
        if (!isset($data["vencInicio"]) or ! isset($data["vencFim"])) {
            unset($data['vencInicio']);
            unset($data['vencFim']);
        }
        if (!isset($data["pagInicio"]) or ! isset($data["pagFim"])) {
            unset($data['pagInicio']);
            unset($data['pagFim']);
        }
        if (!isset($data["compInicio"]) or ! isset($data["compFim"])) {
            unset($data['compInicio']);
            unset($data['compFim']);
        }
        foreach ($data as $key => $value) {
            if ($value == null) {
                unset($data[$key]);
                continue;
            }
            if (array_search($key, ['vencInicio', 'vencFim', 'pagInicio', 'pagFim']) !== FALSE) {
                $data[$key] = $this->ajustarDate($value);
                continue;
            }
            if (array_search($key, ['compInicio', 'compFim']) !== FALSE) {
                $data[$key] = $this->ajustarDate($value, true);
                continue;
            }
        }

        if (isset($data["centrocusto"])) {
            $result = explode('_', $data['centrocusto']);
            if (isset($result[1])) {
                if ($result[1] != '') {
                    $data['categoria'] = (int) $result[1];
                }
            }
            $data['centrocusto'] = (int) $result[0];
        }
        $param = $this->geraQueryCuston($data);
        $repo = $this->em->getRepository($this->entity);
        return $repo->relatorioCustom($param);
    }

    public function geraQueryCuston($data) {
        $bodyQuery = '';
        $parametros = [];
        foreach ($data as $key => $value) {
            if ($key == 'vencInicio') {
                $bodyQuery .= " l.vencimento >=  :vencInicio and ";
                $parametros['vencInicio'] = $value;
                continue;
            }
            if ($key == 'vencFim') {
                $bodyQuery .= " l.vencimento <= :vencFim and ";
                $parametros['vencFim'] = $value;
                continue;
            }
            if ($key == 'pagInicio') {
                $bodyQuery .= " l.pagamento >= :pagInicio and ";
                $parametros['pagInicio'] = $value;
                continue;
            }
            if ($key == 'pagFim') {
                $bodyQuery .= " l.pagamento <= :pagFim and ";
                $parametros['pagFim'] = $value;
                continue;
            }
            if ($key == 'compInicio') {
                $bodyQuery .= " l.competencia >= :compInicio and ";
                $parametros['compInicio'] = $value;
                continue;
            }
            if ($key == 'compFim') {
                $bodyQuery .= " l.competencia <= :compFim and ";
                $parametros['compFim'] = $value;
                continue;
            }
            if ($key == 'tipo') {
                if (!isset($data['valor'])) {
                    if ($value == 'DESPESA') {
                        $bodyQuery .= " l.valor <= :valor and ";
                        $parametros['valor'] = $value;
                        continue;
                    }
                    $bodyQuery .= " l.valor >= :valor and ";
                    $parametros['valor'] = $value;
                    continue;
                }
                continue;
            }
            if ($key == 'descricao') {
                $bodyQuery .= " l.{$key} like :descricao and ";
                $parametros['descricao'] = "%{$value}%";
                continue;
            }
            if ($key == 'documento') {
                $bodyQuery .= " l.{$key} like :documento and ";
                $parametros['documento'] = "%{$value}%";
                continue;
            }
            $bodyQuery .= " l.{$key} = :{$key} and ";
            $parametros[$key] = $value;
        }
        return(['query' => substr($bodyQuery, 0, -4), 'param' => $parametros]);
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
            $VencimentoCorrente = $array["vencimento"]->format("Y-m-d");
            $array["vencimento"] = (new \DateTime($VencimentoCorrente))->add(new \DateInterval("P1M"));
            $array["competencia"] = (new \DateTime($VencimentoCorrente))->add(new \DateInterval("P1M"));
            $registro = $this->hidrate(new Lancamento(), $array);
            if ($registro) {
                $this->em->persist($registro);
            }
        }
        $this->em->flush();
        $this->setMessage("Parcelamento gerado para demais meses!");
    }

    public function historicoSaldos($user) {
        $repo = $this->em->getRepository($this->entity);
        return $repo->historicoSaldos($user);
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
            $comprovantePath = "/profile/{$username}/docs/comprovante_" . time() . "." . substr($files['name']['arquivoComprovante'], -3);
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
        $completePath = __DIR__ . "/../../../../data" . $path;
        if (!file_exists($completePath)) {
            return false;
        }
        if (unlink($completePath)) {
            return true;
        }
        return false;
    }

    public function infoAdditional($user) {
        $repo = $this->em->getRepository($this->entity);
        return $repo->infoAdditional($user);
    }

    public function pagamentoFatura($id_cartao, $user) {
        if ($user) {
            $repo = $this->em->getRepository($this->entity);
            $qtd_registros_atualizados = $repo->pagamentoFatura($id_cartao, $user);
            $this->setMessage("Registros de Cart&atilde;o de cr&eacute;dito atualizados : {$qtd_registros_atualizados}");
            return true;
        }
        $this->setMessage("Pedido com falha!");
        return false;
    }

    public function getDespesasCentroCusto($user, $base_date) {
        $repCcusto = $this->em->getRepository("TVS\Financeiro\Entity\Centrocusto");
        $repLancamento = $this->em->getRepository($this->entity);

        $ccustos = $repCcusto->findBy(['user' => $user]);
        $formatado = [];
        foreach ($ccustos as $ccusto) {
            $formatado[] = $repLancamento->despesasCusto($user, $base_date, $ccusto);
        }
        return $formatado;
    }

}
