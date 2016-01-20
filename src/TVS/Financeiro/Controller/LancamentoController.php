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
        $this->view_new = 'financeiro/lancamento/lancamento_new.html.twig';
        $this->view_edit = 'financeiro/lancamento/lancamento_edit.html.twig';
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
            
        ];
        $this->multiple_forms = [
            'normal' => 'LancamentoForm',
            'parcelada' => 'ParceladoForm',
            'transferencia' => 'TransferenciaForm',
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
        
        $this->controller->post('/edit/mes', function () use ($app) {
            if($app['request']->get('mesreferencia') != ""){
                $dateMes = explode("/", $app['request']->get('mesreferencia'));
                $mes = "{$dateMes[1]}-{$dateMes[0]}";
                $app['session']->set('baseDate',$mes);
            }
            $result = $app[$this->service]->findPagination(0, $this->registros_por_pagina, $this->checkOwner());
            return $app['twig']->render($this->view_list, [
                        $this->param_view => $result,
                        'isAllowed' => $app[$this->service]->isAllowed(true),
                        'bind_path' => $this->bind,
                        'path_table_aditional' => $this->path_table_aditional,
                        'fields_table' => $this->fields_table,
                        'object_key_table' => $this->object_key_table,
                        'page_atual' => 1,
                        'additional' => $app[$this->service]->infoAdditional($this->checkOwner()),
                        'titulo' => $this->titulo,
                        'pagination' => $app[$this->service]->pagination(1, $this->registros_por_pagina, false, false, $this->checkOwner())
            ]);
        })->bind('mes');
    }

}
