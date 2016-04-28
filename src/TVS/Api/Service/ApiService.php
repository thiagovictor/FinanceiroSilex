<?php

namespace TVS\Api\Service;

use TVS\Application;

class ApiService {

    protected $app;

    public function __construct(Application $app) {

        $this->app = $app;
    }

    public function getRealIPAddress() {
        $ip = '';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ip = $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip .= $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip .= $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip .= $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function isValidToken($login, $token) {
        $ip = $this->getRealIPAddress();
        $user = $this->app['LoginService']->findOneBy(['username' => $login]);
        if (!$user) {
            return false;
        }
        $tokenValid = $user->encryptedPassword($login . $ip . date('d/m/Y H'));
        if ($token === $tokenValid) {
            return $user;
        }
        return false;
    }

    public function getToken($login) {
        $ip = $this->getRealIPAddress();
        $user = $this->app['LoginService']->findOneBy(['username' => $login]);
        if (!$user) {
            return false;
        }
        $tokenValid = $user->encryptedPassword($login . $ip . date('d/m/Y'));

        return $tokenValid;
    }

    public function getService($module) {
        $services = ['user' => 'LoginService',
            'route' => 'RouteService',
            'menu' => 'MenuService',
            'privilege' => 'PrivilegeService',
            'profile' => 'ConfigService',
            'periodo' => 'PeriodoService',
            'favorecido' => 'FavorecidoService',
            'centrocusto' => 'CentrocustoService',
            'categoria' => 'CategoriaService',
            'cartao' => 'CartaoService',
            'conta' => 'ContaService',
            'lancamento' => 'LancamentoService',
            'recorrente' => 'RecorrenteService'];
        
        return isset($services[$module])? $services[$module] : false;
    }

    public function ObjectsToArray($array) {
        $objects = [];
        foreach ($array as $value) {
            $objects[] = $value->toArray(true);
        }
        return $objects;
    }

}
