<?php

namespace TVS\Financeiro\Controller;

use TVS\Base\Controller\AbstractController;

class CategoriaController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'CategoriaService';
        $this->form = 'CategoriaForm';
        $this->bind = 'categoria';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->titulo = "Categorias";
        $this->field_search = "descricao";
        $this->fields_table = [
            'DESCRI&Ccedil;&Atilde;O',
            'CENTRO DE CUSTO'
        ];
        $this->object_key_table = [
            ['descricao'],
            ['centrocusto','descricao']
        ];
        $this->is_owner = true;
    }

    public function connect_extra() {
        
    }

}
