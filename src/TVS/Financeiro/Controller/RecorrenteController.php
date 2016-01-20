<?php

namespace TVS\Financeiro\Controller;

use TVS\Base\Controller\AbstractController;

class RecorrenteController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'RecorrenteService';
        $this->form = 'RecorrenteForm';
        $this->bind = 'recorrente';
        $this->param_view = 'result';
        $this->view_new = 'financeiro/lancamento/lancamento_new.html.twig';
        $this->view_edit = 'financeiro/lancamento/lancamento_edit.html.twig';
        $this->view_list = 'login/default/default_list.html.twig';
        $this->titulo = "Lan&ccedil;amentos recorrentes";
        $this->field_search = "descricao";
        $this->fields_table = [
            'INICIAR',
            'DESCRI&Ccedil;&Atilde;O',
            'VALOR',
            'C.CUSTO',
            'CONTA',
            'FAV./PAG.',
            'STATUS'
            
        ];
        $this->object_key_table = [
            ['datetime','vencimento'],
            ['descricao'],
            ['money','valor'],
            ['centrocusto','descricao'],
            ['conta','descricao'],
            ['favorecido','descricao'],
            ['bool','status']
            
        ];

        $this->is_owner = true;
    }

    public function connect_extra() {
    
        
    }

}
