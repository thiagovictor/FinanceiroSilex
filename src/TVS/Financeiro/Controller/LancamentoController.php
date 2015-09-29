<?php

namespace TVS\Financeiro\Controller;

use TVS\Base\Controller\AbstractController;
use TVS\Base\Lib\RepositoryFile;
use Symfony\Component\HttpFoundation\Response;

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
            'VALOR',
            'C.CUSTO',
            'CONTA',
            'FAV./PAG.'
            
        ];
        $this->object_key_table = [
            ['datetime','vencimento'],
            ['datetime','pagamento'],
            ['descricao'],
            ['money','valor'],
            ['centrocusto','descricao'],
            ['conta','descricao'],
            ['favorecido','descricao'],
            //['arquivo','arquivoComprovante'],
            //['arquivo','arquivoBoleto'],
            
        ];
        $this->multiple_forms = [
            'normal' => 'LancamentoForm',
            'recorrente' => 'Lancamento_recorrenteForm',
            'parcelada' => 'ParceladoForm',
            'transferencia' => 'Lancamento_transferenciaForm',
        ];
        $this->is_owner = true;
    }

    public function connect_extra() {
        $app = $this->app;
        $this->controller->get('/display/getBoleto/{id}', function ($id) use ($app) {
            $user = $app['session']->get('user');
            $lancamento = $app['LancamentoService']->find($id, $user);
            if(!$lancamento){
                return false;
            }
            $boleto  = $lancamento->getArquivoBoleto();
            if(!$boleto){
                return false;
            }
            return new Response(
                    (new RepositoryFile("../data".$boleto))->getArquivo(), 200, array(
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'filename="boleto.pdf"'
                    )
            );   
        })->bind('getBoleto');
        
        $this->controller->get('/display/getComprovante/{id}', function ($id) use ($app) {
            $user = $app['session']->get('user');
            $lancamento = $app['LancamentoService']->find($id, $user);
            if(!$lancamento){
                return false;
            }
            $comprovante  = $lancamento->getArquivoComprovante();
            if(!$comprovante){
                return false;
            }
            return new Response(
                    (new RepositoryFile("../data".$comprovante))->getArquivo(), 200, array(
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'filename="comprovante.pdf"'
                    )
            );   
        })->bind('getComprovante');
        
        $this->controller->get('/edit/status/{id}', function ($id) use ($app) {
            $user = $app['session']->get('user');
            $serviceManager = $app['LancamentoService'];
            $lancamento = $serviceManager->find($id, $user);
            if(!$lancamento){
                return false;
            }
            $lancamento->setPagamento(new \DateTime("now"));
            $lancamento->setStatus(1);
            $serviceManager->update($lancamento->toArray(), $this->checkOwner());
            return $app->redirect($app["url_generator"]->generate($this->bind . '_listar'));
        })->bind('editStatus');
    }

}
