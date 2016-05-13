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
            $response = new Response(json_encode($result));
            $response->headers->set("Access-Control-Allow-Origin", "*");
            $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
            $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
            $response->setStatusCode(200);
            return $response;
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
            $response = new Response(json_encode($result));
            $response->headers->set("Access-Control-Allow-Origin", "*");
            $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
            $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
            $response->setStatusCode(200);
            return $response;
        })->bind($this->module . '_api_get')->value('non_require_authentication', true);

        $this->controller->post('/autenticar', function () use ($app) {
            $user = $app['LoginService']->findByUsernameAndPassword($app['request']->get('login'), $app['request']->get('key'));
            if ($user) {
                $response = new Response(json_encode(['token' => $app['ApiService']->getToken($app['request']->get('login'))]));
                $response->headers->set("Access-Control-Allow-Origin", "*");
                $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
                $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
                $response->setStatusCode(200);
                return $response;
            }
            $response = new Response(json_encode('Acesso negado'));
            $response->headers->set("Access-Control-Allow-Origin", "*");
            $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
            $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
            $response->setStatusCode(200);
            return $response;
        })->bind('login_api')->value('non_require_authentication', true);

        $this->controller->get('/info/rest/conta/{login}/{token}', function ($login, $token) use ($app) {
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $app['session']->set('baseDate', date("Y-m"));
            $result = $app['ContaService']->infoAdditional($user);

            $response = new Response(json_encode($result));
            $response->headers->set("Access-Control-Allow-Origin", "*");
            $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
            $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
            $response->setStatusCode(200);
            return $response;
        })->bind('conta_saldos_api_get')->value('non_require_authentication', true);

        $this->controller->get('/totais/rest/conta/{login}/{token}', function ($login, $token) use ($app) {
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $app['session']->set('baseDate', date("Y-m"));
            $result = $app['LancamentoService']->infoAdditional($user);

            $response = new Response(json_encode([$result]));
            $response->headers->set("Access-Control-Allow-Origin", "*");
            $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
            $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
            $response->setStatusCode(200);
            return $response;
        })->bind('conta_totais_api_get')->value('non_require_authentication', true);

        $this->controller->post('/cadastro/lancamento/{option}/{login}/{token}', function ($option, $login, $token) use ($app) {
            $user = $app['ApiService']->isValidToken($login, $token);
            if ($user === false) {
                return new Response('User False');
            }
            $app['session']->set('user', $user);
            $app['session']->set('baseDate', date("Y-m"));
            $attributes = [
                'option',
                'descricao',
                'documento',
                'tipo',
                'centrocusto',
                'favorecido',
                'conta',
                'cartao',
                'status'
            ];
            $lancamento = [];
            $fp = fopen("logsHoje.txt", "a+");
            //"Sat May 07 2016 00:00:00 GMT-0300 (BRT)"
            // Sat May 07 2016 00:00:00 GMT-0300 (Hora oficial do Brasil)
            // fwrite($fp, 'Vencimento:' . $app['request']->get('pagamento') . "\r\n");
            $lancamento['vencimento'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('vencimento'))))->format("d/m/Y");
            if ($app['request']->get('pagamento') !== 'null') {
                $lancamento['pagamento'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('pagamento'))))->format("d/m/Y");
            }
            $lancamento['competencia'] = (new \DateTime(str_replace("(Hora oficial do Brasil)", "(BRT)", $app['request']->get('competencia'))))->format("m/Y");

            $lancamento['valor'] = $this->moedaToDecimal($app['request']->get('valor'));

            fwrite($fp, 'Vencimento:' . $lancamento['vencimento'] . "\r\n");
            if ($app['request']->get('pagamento') !== 'null') {
                fwrite($fp, 'Pagamento:' . $lancamento['pagamento'] . "\r\n");
            }
            fwrite($fp, 'Competencia:' . $lancamento['competencia'] . "\r\n");

            foreach ($attributes as $value) {
                if ($app['request']->get($value)) {
                    $lancamento[$value] = $app['request']->get($value);
                    fwrite($fp, $value . ':' . $app['request']->get($value) . "\r\n");
                }
            }
            fclose($fp);
            $app['LancamentoService']->insert($lancamento);
            $response = new Response("teste");
            $response->headers->set("Access-Control-Allow-Origin", "*");
            $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
            $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
            $response->setStatusCode(200);
            return $response;
        })->bind('cadastro_lancamento')->value('non_require_authentication', true);

        return $this->controller;
    }

}
