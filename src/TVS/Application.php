<?php

namespace TVS;

use Silex\Application as ApplicationSilex;
use TVS\Login\Controller\LoginController;
use TVS\Login\Controller\RouteController;
use TVS\Login\Controller\PrivilegeController;
use TVS\Login\Controller\ProfileController;
use TVS\Login\Controller\ConfigController;
use TVS\Financeiro\Controller\TipoController;
use TVS\Financeiro\Controller\PeriodoController;
use TVS\Financeiro\Controller\FavorecidoController;
use TVS\Financeiro\Controller\CentrocustoController;
use TVS\Financeiro\Controller\CategoriaController;
use TVS\Financeiro\Controller\CartaoController;
use TVS\Login\Controller\MenuController;
use Symfony\Component\HttpFoundation\Request;

class Application extends ApplicationSilex {

    public function __construct(array $values = array()) {
        parent::__construct($values);
        $app = $this;

        $services = [
            ['name' => 'LoginService', 'service' => 'TVS\Login\Service\LoginService', 'entity' => 'TVS\Login\Entity\User'],
            ['name' => 'RouteService', 'service' => 'TVS\Login\Service\RouteService', 'entity' => 'TVS\Login\Entity\Route'],
            ['name' => 'MenuService', 'service' => 'TVS\Login\Service\MenuService', 'entity' => 'TVS\Login\Entity\Menu'],
            ['name' => 'PrivilegeService', 'service' => 'TVS\Login\Service\PrivilegeService', 'entity' => 'TVS\Login\Entity\Privilege'],
            ['name' => 'ConfigService', 'service' => 'TVS\Login\Service\ConfigService', 'entity' => 'TVS\Login\Entity\Config'],
            ['name' => 'TipoService', 'service' => 'TVS\Financeiro\Service\TipoService', 'entity' => 'TVS\Financeiro\Entity\Tipo'],
            ['name' => 'PeriodoService', 'service' => 'TVS\Financeiro\Service\PeriodoService', 'entity' => 'TVS\Financeiro\Entity\Periodo'],
            ['name' => 'FavorecidoService', 'service' => 'TVS\Financeiro\Service\FavorecidoService', 'entity' => 'TVS\Financeiro\Entity\Favorecido'],
            ['name' => 'CentrocustoService', 'service' => 'TVS\Financeiro\Service\CentrocustoService', 'entity' => 'TVS\Financeiro\Entity\Centrocusto'],
            ['name' => 'CategoriaService', 'service' => 'TVS\Financeiro\Service\CategoriaService', 'entity' => 'TVS\Financeiro\Entity\Categoria'],
            ['name' => 'CartaoService', 'service' => 'TVS\Financeiro\Service\LoginService', 'entity' => 'TVS\Financeiro\Entity\Cartao'],
            ['name' => 'LDAP', 'service' => 'TVS\Base\Lib\ConnectionLDAP'],
        ];

        $forms = [
            ['name' => 'UserForm', 'type' => 'TVS\Login\Form\UserType'],
            ['name' => 'UserFormEdit', 'type' => 'TVS\Login\Form\UserEditType'],
            ['name' => 'MenuForm', 'type' => 'TVS\Login\Form\MenuType'],
            ['name' => 'PrivilegeForm', 'type' => 'TVS\Login\Form\PrivilegeType', 'injection' => true],
            ['name' => 'ProfileForm', 'type' => 'TVS\Login\Form\ProfileType'],
            ['name' => 'ConfigForm', 'type' => 'TVS\Login\Form\ConfigType'],
            ['name' => 'TipoForm', 'type' => 'TVS\Financeiro\Form\TipoType'],
            ['name' => 'PeriodoForm', 'type' => 'TVS\Financeiro\Form\PeriodoType'],
            ['name' => 'FavorecidoForm', 'type' => 'TVS\Financeiro\Form\FavorecidoType'],
            ['name' => 'CentrocustoForm', 'type' => 'TVS\Financeiro\Form\CentrocustoType'],
            ['name' => 'CategoriaForm', 'type' => 'TVS\Financeiro\Form\CategoriaType', 'injection' => true],
            ['name' => 'CartaoForm', 'type' => 'TVS\Financeiro\Form\CartaoType'],
        ];

        foreach ($services as $service) {
            $this->registerServices($service);
        }
        foreach ($forms as $form) {
            $this->registerForms($form);
        }

        $app->before(function(Request $request) use ($app) {
            if (!$request->get('non_require_authentication')) {
                if (!$app['session']->get('user')) {
                    return $app->redirect('/');
                }
                if (!$app['PrivilegeService']->isAllowed()) {
                    $app['session']->set('error', "Acesso Negado! Permiss&otilde;es insuficientes.");
                    return $app->redirect('/');
                }
            }
        });

        $app->get('/', function () use ($app) {
                    $params = [];
                    if ($app['session']->get('error')) {
                        if (!empty($app['session']->get('error'))) {
                            $params = ['result' => $app['session']->get('error')];
                            $app['session']->set('error', null);
                        }
                    }
                    return $app['twig']->render('login/login.twig', $params);
                })->bind('login')
                ->value('non_require_authentication', true);

        $app->get('/index', function () use ($app) {
            return $app['twig']->render('template.twig', []);
        })->bind('inicio');

        $app->get('/logout', function() use ($app) {
            $app['session']->clear();
            return $app->redirect('/');
        })->bind('logout');

        $app->mount("/user", new LoginController());
        $app->mount("/route", new RouteController());
        $app->mount("/menu", new MenuController());
        $app->mount("/privilege", new PrivilegeController());
        $app->mount("/profile", new ProfileController());
        $app->mount("/config", new ConfigController());
        $app->mount("/tipo", new TipoController());
        $app->mount("/periodo", new PeriodoController());
        $app->mount("/favorecido", new FavorecidoController());
        $app->mount("/centrocusto", new CentrocustoController());
        $app->mount("/categoria", new CategoriaController());
        $app->mount("/cartao", new CartaoController());
    }

    public function registerServices($options) {
        $app = $this;
        $app[$options['name']] = function () use($app, $options) {
            if (isset($options['entity'])) {
                return new $options['service']($app['EntityManager'], new $options['entity'], $app);
            }
            return new $options['service']($app);
        };
    }

    public function registerForms($options) {
        $app = $this;
        $app[$options['name']] = function () use($app, $options) {
            if (isset($options['injection'])) {
                return $app['form.factory']->createBuilder(new $options['type']($app))->getForm();
            }
            return $app['form.factory']->createBuilder(new $options['type']())->getForm();
        };
    }

}
