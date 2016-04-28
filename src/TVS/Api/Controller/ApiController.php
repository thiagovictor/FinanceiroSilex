<?php

namespace TVS\Api\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class ApiController implements ControllerProviderInterface {
    protected $controller;
    protected $app;
    protected $module;


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
            $order = [];
            if ($module == 'lancamento') {
                $busca['competencia'] = new \DateTime(date('Y-m') . '-01');
                $order = ['pagamento' => 'ASC', 'vencimento' => 'ASC'];
            }
            $result =  $app['ApiService']->ObjectsToArray($app[$app['ApiService']->getService($module)]->findBy($busca,$order));
            $response = new Response(json_encode($result));
            $response->headers->set("Access-Control-Allow-Origin", "*");
            $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
            $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
            $response->setStatusCode(200);
            return $response;
            
        })->bind($this->module . '_api_listar')->value('non_require_authentication', true);

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

        return $this->controller;
    }

}