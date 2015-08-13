<?php

namespace TVS;

use Silex\Application as ApplicationSilex;
use TVS\Login\Controller\LoginController;
use TVS\Login\Controller\RouteController;
use TVS\Login\Controller\PrivilegeController;
use TVS\Login\Controller\ProfileController;
use TVS\Login\Controller\ConfigController;
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
        
        $app['LDAP'] = function () use($app){
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
    }

}
