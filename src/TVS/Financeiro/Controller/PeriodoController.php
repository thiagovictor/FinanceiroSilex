<?php

namespace TVS\Financeiro\Controller;

use TVS\Base\Controller\AbstractController;

class PeriodoController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'PeriodoService';
        $this->form = 'PeriodoForm';
        $this->bind = 'periodo';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->titulo = "Intervalos de lan&ccedil;amento recorrente";
        $this->field_search = "descricao";
        $this->fields_table = [
            'ID',
            'DESCRI&Ccedil;&Atilde;O'
        ];
        $this->object_key_table = [
            ['id'],
            ['descricao']
        ];
    }

    public function connect_extra() {
        
    }

}
