<?php

namespace TVS\Login\Controller;

use TVS\Base\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'LoginService';
        $this->bind = 'user';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->form = 'UserForm';
        $this->form_edit = 'UserFormEdit';
        $this->titulo = "Usu&aacute;rios";
        $this->field_search = "username";
        $this->fields_table = [
            'ID',
            'NOME',
            'EMAIL',
            'ATIVO',
            'SYNC'
        ];
        $this->object_key_table = [
            ['id'],
            ['username'],
            ['email'],
            ['bool','ativo'],
            ['bool','ad']
        ];
        $this->path_table_aditional = ['editPrivilegeAllUser'=>'glyphicon glyphicon-lock'];
    }

    public function connect_extra() {
        $app = $this->app;
        $this->controller->post('/autenticar', function (Request $request) use ($app) {
            $user = $app[$this->service]->findByUsernameAndPassword($request->get('user'), $request->get('password'));
            if($user){
                $app['session']->set('user', $user);
                return $app->redirect('/index');
            }
            return $app['twig']->render('login/login.twig', [$this->param_view  => "Usu&aacute;rio e/ou Senha Incorretos", "user"=>$request->get('user')]);
        })->bind($this->bind . '_autenticar')
          ->value('non_require_authentication', true);
    }

}
