<?php

namespace TVS\Financeiro\Controller;

use TVS\Base\Controller\AbstractController;

class LancamentoController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'LancamentoService';
        $this->form = 'LancamentoForm';
        $this->bind = 'lancamento';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/default/default_edit.twig';
        $this->view_list = 'financeiro/lancamento/list.html.twig';
        $this->titulo = "Lancamentos";
        $this->field_search = "descricao";
        $this->fields_table = [
            'VENCIMENTO',
            'PAGAMENTO',
            'DESCRI&Ccedil;&Atilde;O',
            //'DOC',
            'VALOR',
            'C.CUSTO',
            //'CATEGORIA',
            'CONTA',
            'FAV./PAG.'
            
        ];
        $this->object_key_table = [
            ['datetime','vencimento'],
            ['datetime','pagamento'],
            ['descricao'],
            //['documento'],
            ['money','valor'],
            ['centrocusto','descricao'],
            //['categoria','descricao'],
            ['conta','descricao'],
            ['favorecido','descricao']
        ];
        $this->multiple_forms = [
            'normal' => 'LancamentoForm',
            'recorrente' => 'Lancamento_recorrenteForm',
            'parcelada' => 'Lancamento_parceladoForm',
            'transferencia' => 'Lancamento_transferenciaForm',
        ];
        $this->is_owner = true;
    }

    public function connect_extra() {
        
    }

}
