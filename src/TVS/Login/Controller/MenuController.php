<?php

namespace TVS\Login\Controller;

use TVS\Base\Controller\AbstractController;

class MenuController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'MenuService';
        $this->form = 'MenuForm';
        $this->bind = 'menu';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->titulo = "Menu";
        $this->field_search = "desc";
        $this->fields_table = [
            'ID',
            'MENU',
            'DESCRI&Ccedil;&Atilde;O',
        ];
        $this->object_key_table = [
            ['id'],
            ['label'],
            ['desc'],
        ];
    }

    public function connect_extra() {
        $app = $this->app;
        $this->controller->get('/display/dinamicmenu', function () use ($app) {
            $result = $app[$this->service]->getMenu();
            return $app['twig']->render('login/menu/dinamic_menu.html.twig', [
                        'result' => $result,
            ]);
        })->bind('DinamicMenu');

        $this->controller->get('/display/preferencemenu', function () use ($app) {
            return $app['twig']->render('login/menu/preference_menu.html.twig', [
                        'user' => $app['session']->get('user'),
                        'isAllowed' => $app['LancamentoService']->isAllowed(true)
            ]);
        })->bind('PreferenceMenu');
    }

}
