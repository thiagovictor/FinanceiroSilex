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

        $app['LoginService'] = function () use($app) {
            return new Login\Service\LoginService($app['EntityManager'], new Login\Entity\User(), $app);
        };

        $app['UserForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Login\Form\UserType())->getForm();
        };

        $app['UserFormEdit'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Login\Form\UserEditType())->getForm();
        };

        $app['RouteService'] = function () use($app) {
            return new Login\Service\RouteService($app['EntityManager'], new Login\Entity\Route(), $app);
            ;
        };

        $app['RouteForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Login\Form\RouteType($app))->getForm();
        };

        $app['MenuService'] = function () use($app) {
            return new Login\Service\MenuService($app['EntityManager'], new Login\Entity\Menu(), $app);
        };

        $app['MenuForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Login\Form\MenuType())->getForm();
        };

        $app['PrivilegeService'] = function () use($app) {
            $privileService = new Login\Service\PrivilegeService($app['EntityManager'], new Login\Entity\Privilege(), $app);
            return $privileService;
        };

        $app['PrivilegeForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Login\Form\PrivilegeType($app))->getForm();
        };

        $app['ProfileForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Login\Form\ProfileType())->getForm();
        };

        $app['ConfigService'] = function () use($app) {
            return new Login\Service\ConfigService($app['EntityManager'], new Login\Entity\Config(), $app);
        };

        $app['ConfigForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Login\Form\ConfigType())->getForm();
        };

        $app['TipoService'] = function () use($app) {
            return new Financeiro\Service\TipoService($app['EntityManager'], new Financeiro\Entity\Tipo(), $app);
        };

        $app['TipoForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Financeiro\Form\TipoType())->getForm();
        };

        $app['PeriodoService'] = function () use($app) {
            return new Financeiro\Service\PeriodoService($app['EntityManager'], new Financeiro\Entity\Periodo(), $app);
        };

        $app['PeriodoForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Financeiro\Form\PeriodoType())->getForm();
        };

        $app['FavorecidoService'] = function () use($app) {
            return new Financeiro\Service\FavorecidoService($app['EntityManager'], new Financeiro\Entity\Favorecido(), $app);
        };

        $app['FavorecidoForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Financeiro\Form\FavorecidoType())->getForm();
        };

        $app['CentrocustoService'] = function () use($app) {
            return new Financeiro\Service\CentrocustoService($app['EntityManager'], new Financeiro\Entity\Centrocusto(), $app);
        };

        $app['CentrocustoForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Financeiro\Form\CentrocustoType())->getForm();
        };

        $app['CategoriaService'] = function () use($app) {
            return new Financeiro\Service\CategoriaService($app['EntityManager'], new Financeiro\Entity\Categoria(), $app);
        };

        $app['CategoriaForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Financeiro\Form\CategoriaType($app))->getForm();
        };

        $app['CartaoService'] = function () use($app) {
            return new Financeiro\Service\CartaoService($app['EntityManager'], new Financeiro\Entity\Cartao(), $app);
        };

        $app['CartaoForm'] = function () use($app) {
            return $app['form.factory']->createBuilder(new Financeiro\Form\cartaoType())->getForm();
        };

        $app['LDAP'] = function () use($app) {
            return new \TVS\Base\Lib\ConnectionLDAP($app);
        };

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

}
