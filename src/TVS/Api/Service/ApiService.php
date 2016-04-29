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
        $tokenValid = $user->encryptedPassword($login . $ip . date('d/m/Y'));
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

        return isset($services[$module]) ? $services[$module] : false;
    }

    public function ObjectsToArray($array) {
        $objects = [];
        foreach ($array as $value) {
            $objects[] = $this->classToArray($value);
        }
        return $objects;
    }

    public function classToArray($class) {
        if (!is_object($class)) {
            return false;
        }
        $methods = $this->methodGet($class);
        $classArray = [];
        foreach ($methods as $key => $method) {
            $value = $class->$method();
            if ($value instanceof \DateTime) {
                $value = $value->format('d/m/Y');
                $classArray[strtolower(substr($method, 3, strlen($method)))] = $value;
                continue;
            }
            if (is_object($value)) {
                $value = $value->toArray();
                $classArray[strtolower(substr($method, 3, strlen($method)))] = $value;
                continue;
            }
            $classArray[strtolower(substr($method, 3, strlen($method)))] = $value;
        }
        return $classArray;
    }

    public function methodGet($class) {
        $methods = get_class_methods($class);
        foreach ($methods as $key => $method) {
            if (substr($method, 0, 3) != 'get') {
                unset($methods[$key]);
                continue;
            }
            if ($method == 'getUser') {
                unset($methods[$key]);
                continue;
            }
        }
        return $methods;
    }

}
