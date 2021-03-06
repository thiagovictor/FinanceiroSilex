<?php

namespace TVS\Financeiro\Controller;

use TVS\Base\Controller\AbstractController;

class FavorecidoController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'FavorecidoService';
        $this->form = 'FavorecidoForm';
        $this->bind = 'favorecido';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->titulo = "Favorecidos";
        $this->field_search = "descricao";
        $this->fields_table = [
            'DESCRI&Ccedil;&Atilde;O'
        ];
        $this->object_key_table = [
            ['descricao']
        ];
        $this->is_owner = true;
    }

    public function connect_extra() {
        
    }

}
