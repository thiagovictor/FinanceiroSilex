<?php

namespace TVS\Login\Controller;

use TVS\Base\Controller\AbstractController;

class ConfigController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'ConfigService';
        $this->form = 'ConfigForm';
        $this->bind = 'config';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->titulo = "Par&acirc;metros de sistema";
        $this->field_search = "nome";
        $this->fields_table = [
            'ID',
            'NOME',
        ];
        $this->object_key_table = [
            ['id'],
            ['nome'],
        ];
    }
}
