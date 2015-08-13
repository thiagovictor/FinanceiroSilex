<?php

namespace TVS\Login\Controller;

use TVS\Base\Controller\AbstractController;

class RouteController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'RouteService';
        $this->form = 'RouteForm';
        $this->bind = 'route';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->titulo = "M&oacute;dulos";
        $this->field_search = "route";
        $this->fields_table = [
            'ID',
            'M&Oacute;DULO',
            'DESCRI&Ccedil;&Atilde;O',
            'MENU'
        ];
        $this->object_key_table = [
            ['id'],
            ['route'],
            ['label'],
            ['menu','label']
        ];
    }

    public function connect_extra() {
        
    }

}
