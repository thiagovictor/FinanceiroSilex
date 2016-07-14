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
use TVS\Financeiro\Controller\ContaController;
use TVS\Financeiro\Controller\LancamentoController;
use TVS\Financeiro\Controller\RecorrenteController;
use TVS\Login\Controller\MenuController;
use Symfony\Component\HttpFoundation\Request;
use TVS\Api\Controller\ApiController;
use Symfony\Component\HttpFoundation\Session\Session;

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
            ['name' => 'CartaoService', 'service' => 'TVS\Financeiro\Service\CartaoService', 'entity' => 'TVS\Financeiro\Entity\Cartao'],
            ['name' => 'ContaService', 'service' => 'TVS\Financeiro\Service\ContaService', 'entity' => 'TVS\Financeiro\Entity\Conta'],
            ['name' => 'LancamentoService', 'service' => 'TVS\Financeiro\Service\LancamentoService', 'entity' => 'TVS\Financeiro\Entity\Lancamento'],
            ['name' => 'RecorrenteService', 'service' => 'TVS\Financeiro\Service\RecorrenteService', 'entity' => 'TVS\Financeiro\Entity\Recorrente'],
            ['name' => 'LDAP', 'service' => 'TVS\Base\Lib\ConnectionLDAP'],
            ['name' => 'ApiService', 'service' => 'TVS\Api\Service\ApiService'],
        ];

        $forms = [
            ['name' => 'UserForm', 'type' => 'TVS\Login\Form\UserType'],
            ['name' => 'UserFormEdit', 'type' => 'TVS\Login\Form\UserEditType'],
            ['name' => 'RouteForm', 'type' => 'TVS\Login\Form\RouteType', 'injection' => true],
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
            ['name' => 'ContaForm', 'type' => 'TVS\Financeiro\Form\ContaType'],
            ['name' => 'LancamentoForm', 'type' => 'TVS\Financeiro\Form\LancamentoType', 'injection' => true],
            ['name' => 'CustomRelatorioForm', 'type' => 'TVS\Financeiro\Form\RelatorioCustomizadoType', 'injection' => true],
            ['name' => 'ParceladoForm', 'type' => 'TVS\Financeiro\Form\ParceladoType', 'injection' => true],
            ['name' => 'TransferenciaForm', 'type' => 'TVS\Financeiro\Form\TransferenciaType', 'injection' => true],
            ['name' => 'RecorrenteForm', 'type' => 'TVS\Financeiro\Form\RecorrenteType', 'injection' => true],
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
            
            $view_list = 'index.twig';
            $result = $app['ContaService']->infoAdditional((new Session())->get('user'));
            return $app['twig']->render($view_list, [
                        'result' => $result,
                        'titulo' => 'Informa&ccedil;&otilde;es gerais',
                        'totais' => $app['LancamentoService']->infoAdditional((new Session())->get('user'))
            ]);
            
            
            
            
            //return $app['twig']->render('index.twig', []);
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
        $app->mount("/conta", new ContaController());
        $app->mount("/lancamento", new LancamentoController());
        $app->mount("/recorrente", new RecorrenteController());
        $app->mount("/api", new ApiController());
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
