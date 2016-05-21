<?php

namespace TVS\Api\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class ApiController implements ControllerProviderInterface {

    protected $controller;
    protected $app;
    protected $module;

    function moedaToDecimal($get_valor) {
        $source = array('.', ',');
        $replace = array('', '.');
        $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
        return $valor; //retorna o valor formatado para gravar no banco
    }

    function ResponseApi($content) {
        $response = new Response(json_encode($content));
        $response->headers->set("Access-Control-Allow-Origin", "*");
        $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
        $response->setStatusCode(200);
        return $response;
    }
    
    public function logger($lancamento) {
        $attributes = ['option','id','vencimento','competencia','pagamento','valor', 'descricao','documento','tipo','centrocusto','favorecido','conta','cartao','status'];
        $fp = fopen("logsAPI.txt", "a+");
        foreach ($_POST as $key => $value) {
            fwrite($fp, $key . "(SESSION):  " . $_POST[$key] . "\r\n");
        }
        foreach ($lancamento as $key => $value) {
            fwrite($fp, $key . "(LOCAL):  " . $lancamento[$key] . "\r\n");
        }
        fwrite($fp, "#############################################################\r\n");
        fclose($fp);
    }

    public function connect(Application $app) {
        $this->controller = $app['controllers_factory'];
        $this->app = $app;

        $this->controller->get('/rest/{module}/{login}/{token}', function ($module, $login, $token) use ($app) {
            $this->module = $module;
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $busca = ['user' => $user];
            $order = ['descricao' => 'ASC'];
            if ($module == 'lancamento') {
                $busca['competencia'] = new \DateTime(date('Y-m') . '-01');
                $order = ['pagamento' => 'ASC', 'vencimento' => 'ASC'];
            }

            $result = $app['ApiService']->ObjectsToArray($app[$app['ApiService']->getService($module)]->findBy($busca, $order));
            if ($module == 'centrocusto') {
                $app['session']->set('user', $user);
                $resultTemp = $app[$app['ApiService']->getService($module)]->selecao();
                $result = [];
                foreach ($resultTemp as $key => $value) {
                    $result[] = [
                        'id' => $key,
                        'descricao' => $value
                    ];
                }
            }
            return $this->ResponseApi($result);
        })->bind($this->module . '_api_listar')->value('non_require_authentication', true);

        $this->controller->get('/rest/{module}/{login}/{token}/{id}', function ($module, $login, $token, $id) use ($app) {
            $this->module = $module;
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $busca = ['user' => $user,
                'id' => $id];
            $result = $app['ApiService']->ObjectOneToArray($app[$app['ApiService']->getService($module)]->findOneBy($busca));
            return $this->ResponseApi($result);
        })->bind($this->module . '_api_get')->value('non_require_authentication', true);

        $this->controller->delete('/rest/{module}/{login}/{token}/{id}', function ($module, $login, $token, $id) use ($app) {
            $this->module = $module;
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $result = $app[$app['ApiService']->getService($module)]->delete($id, $user);
            return $this->ResponseApi(json_encode($result));
        })->bind($this->module . '_api_delete')->value('non_require_authentication', true);

        $this->controller->options('/rest/{module}/{login}/{token}/{id}', function ($module, $login, $token, $id) use ($app) {
            return $this->ResponseApi("true");
        })->bind($this->module . '_api_options')->value('non_require_authentication', true);

        $this->controller->post('/autenticar', function () use ($app) {
            $user = $app['LoginService']->findByUsernameAndPassword($app['request']->get('login'), $app['request']->get('key'));
            if ($user) {
                return $this->ResponseApi(['token' => $app['ApiService']->getToken($app['request']->get('login'))]);
            }
            return $this->ResponseApi('Acesso negado');
        })->bind('login_api')->value('non_require_authentication', true);

        $this->controller->get('/info/rest/conta/{login}/{token}', function ($login, $token) use ($app) {
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $app['session']->set('baseDate', date("Y-m"));
            $result = $app['ContaService']->infoAdditional($user);
            return $this->ResponseApi($result);
        })->bind('conta_saldos_api_get')->value('non_require_authentication', true);

        $this->controller->get('/totais/rest/conta/{login}/{token}', function ($login, $token) use ($app) {
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $app['session']->set('baseDate', date("Y-m"));
            $result = $app['LancamentoService']->infoAdditional($user);
            return $this->ResponseApi([$result]);
        })->bind('conta_totais_api_get')->value('non_require_authentication', true);

        $this->controller->post('/cadastro/lancamento/{option}/{login}/{token}', function ($option, $login, $token) use ($app) {
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $app['session']->set('user', $user);
            $app['session']->set('baseDate', date("Y-m"));
            $attributes = ['descricao','documento','tipo','centrocusto','favorecido','conta','cartao','status'];
            if ($option == 'transferencia') {
                $attributes[] = 'conta2';
            }
            if ($option == 'parcelado') {
                $attributes[] = 'parcelas';
                $option = 'normal';
            }
            $lancamento = [];
            $lancamento['option'] = $option;
            $lancamento['vencimento'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('vencimento'))))->format("d/m/Y");
            $lancamento['competencia'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('competencia'))))->format("m/Y");
            $lancamento['valor'] = $this->moedaToDecimal($app['request']->get('valor'));
            if ($app['request']->get('pagamento') !== 'null') {
                $lancamento['pagamento'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('pagamento'))))->format("d/m/Y");
            }
            foreach ($attributes as $value) {
                if ($app['request']->get($value)) {
                    $lancamento[$value] = $app['request']->get($value);
                }
            }
            if(!isset($lancamento['status'])){
                $lancamento['status'] = false;
            }
            $this->logger($lancamento);
            $app['LancamentoService']->insert($lancamento);
            return $this->ResponseApi("true");
        })->bind('cadastro_lancamento')->value('non_require_authentication', true);
        
        $this->controller->options('/cadastro/lancamento/{option}/{login}/{token}', function ($option, $login, $token) use ($app) {
            return $this->ResponseApi("true");
        })->bind('cadastro_lancamento_options')->value('non_require_authentication', true);
        
        $this->controller->put('/cadastro/lancamento/{option}/{login}/{token}', function ($option, $login, $token) use ($app) {
            
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $app['session']->set('user', $user);
            $app['session']->set('baseDate', date("Y-m"));
            $attributes = ['id','descricao','documento','tipo','centrocusto','favorecido','conta','cartao','status'];
            
            $lancamento = [];
            $lancamento['option'] = $option;
            $lancamento['vencimento'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('vencimento'))))->format("d/m/Y");
            $lancamento['competencia'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('competencia'))))->format("m/Y");
            $lancamento['valor'] = $this->moedaToDecimal($app['request']->get('valor'));
            if ($app['request']->get('pagamento') !== 'null') {
                $lancamento['pagamento'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('pagamento'))))->format("d/m/Y");
            }
            foreach ($attributes as $value) {
                if ($app['request']->get($value) == 'null') {
                    $lancamento[$value] = NULL;
                    continue;
                }
                if ($app['request']->get($value) == 'false') {
                    $lancamento[$value] = false;
                    continue;
                }
                if ($app['request']->get($value)) {
                    $lancamento[$value] = $app['request']->get($value);
                }
            }
            if(!isset($lancamento['status'])){
                $lancamento['status'] = false;
            }
            $this->logger($lancamento);
            $app['LancamentoService']->update($lancamento,$user);
            return $this->ResponseApi("true");
            
        })->bind('cadastro_lancamento_update')->value('non_require_authentication', true);
        return $this->controller;
    }

}
