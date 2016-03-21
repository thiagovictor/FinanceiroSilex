<?php

namespace TVS\Financeiro\Controller;

use TVS\Base\Controller\AbstractController;
use TVS\Base\Lib\RepositoryFile;
use Symfony\Component\HttpFoundation\Response;

class LancamentoController extends AbstractController {

    public function __construct() {
        $this->registros_por_pagina = 6;
        $this->service = 'LancamentoService';
        $this->form = 'LancamentoForm';
        $this->bind = 'lancamento';
        $this->param_view = 'result';
        $this->view_new = 'financeiro/lancamento/lancamento_new.html.twig';
        $this->view_custom = 'financeiro/lancamento/lancamento_search_custom.html.twig';
        $this->view_edit = 'financeiro/lancamento/lancamento_edit.html.twig';
        $this->view_list = 'financeiro/lancamento/list.html.twig';
        $this->titulo = "Lancamentos";
        $this->field_search = "descricao";
        $this->fields_table = [
            'VENC.',
            'PAG.',
            'DESCRI&Ccedil;&Atilde;O',
            'VALOR',
            'C.CUSTO',
            'CONTA',
            //'FAV./PAG.'
        ];
        $this->object_key_table = [
            ['datetime', 'vencimento'],
            ['datetime', 'pagamento'],
            ['descricao'],
            ['money', 'valor'],
            ['centrocusto', 'descricao'],
            ['conta', 'descricao'],
            //['favorecido', 'descricao'],
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
            if (!$lancamento) {
                return false;
            }
            $boleto = $lancamento->getArquivoBoleto();
            if (!$boleto) {
                return false;
            }
            return new Response(
                    (new RepositoryFile("../data" . $boleto))->getArquivo(), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'filename="boleto.pdf"'
                    )
            );
        })->bind('getBoleto');

        $this->controller->get('/display/getComprovante/{id}', function ($id) use ($app) {
            $user = $app['session']->get('user');
            $lancamento = $app['LancamentoService']->find($id, $user);
            if (!$lancamento) {
                return false;
            }
            $comprovante = $lancamento->getArquivoComprovante();
            if (!$comprovante) {
                return false;
            }
            return new Response(
                    (new RepositoryFile("../data" . $comprovante))->getArquivo(), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'filename="comprovante.pdf"'
                    )
            );
        })->bind('getComprovante');

        $this->controller->get('/edit/status/{id}', function ($id) use ($app) {
            $user = $app['session']->get('user');
            $serviceManager = $app['LancamentoService'];
            $lancamento = $serviceManager->find($id, $user);
            if (!$lancamento) {
                return false;
            }
            $lancamento->setPagamento(new \DateTime("now"));
            $lancamento->setStatus(1);
            $serviceManager->update($lancamento->toArray(), $this->checkOwner());
            return $app->redirect($app["url_generator"]->generate($this->bind . '_listar'));
        })->bind('editStatus');
        
        $this->controller->get('/edit/status/cartao/{id}', function ($id) use ($app) {
            $user = $app['session']->get('user');
            $serviceManager = $app['LancamentoService'];
            $serviceManager->pagamentoFatura($id,$user);

            $result = $app[$this->service]->findPagination(0, $this->registros_por_pagina, $this->checkOwner());
            return $app['twig']->render($this->view_list, [
                        $this->param_view => $result,
                        'isAllowed' => $app[$this->service]->isAllowed(true),
                        'bind_path' => $this->bind,
                        'path_table_aditional' => $this->path_table_aditional,
                        'fields_table' => $this->fields_table,
                        'object_key_table' => $this->object_key_table,
                        'page_atual' => 1,
                        "Message" => $serviceManager->getMessage(),
                        'additional' => $app[$this->service]->infoAdditional($this->checkOwner()),
                        'titulo' => $this->titulo,
                        'pagination' => $app[$this->service]->pagination(1, $this->registros_por_pagina, false, false, $this->checkOwner())
            ]);
            
            
        })->bind('editStatusCartao');

        $this->controller->post('/edit/mes', function () use ($app) {
            if ($app['request']->get('mesreferencia') != "") {
                $dateMes = explode("/", $app['request']->get('mesreferencia'));
                $mes = "{$dateMes[1]}-{$dateMes[0]}";
                $app['session']->set('baseDate', $mes);
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

        $this->controller->get('/display/fatura/{id}', function ($id) use ($app) {
            $fields_table = [
                'DESCRI&Ccedil;&Atilde;O',
                'VALOR',
                'C.CUSTO',
                'CONTA',
                'FAV./PAG.'
            ];
            $object_key_table = [
                ['descricao'],
                ['money', 'valor'],
                ['centrocusto', 'descricao'],
                ['conta', 'descricao'],
                ['favorecido', 'descricao'],
            ];
            $base_date = new \DateTime((new \Symfony\Component\HttpFoundation\Session\Session())->get('baseDate') . "-01");

            $result = $app[$this->service]->findBy(['user' => $this->checkOwner(), 'cartao' => $id, 'competencia' => $base_date]);
            return $app['twig']->render('financeiro/cartao/fatura.html.twig', [
                        $this->param_view => $result,
                        'fields_table' => $fields_table,
                        'object_key_table' => $object_key_table,
            ]);
        })->bind($this->bind . '_credito_fatura');
        
        $this->controller->match('/display/consultas/personalizado', function () use ($app) {
            $form = $app['CustomRelatorioForm'];
            $form->handleRequest($app['request']);
            $serviceManager = $app[$this->service];
            if ($form->isValid()) {
                $data = $form->getData();
                $result = $serviceManager->relatorioCustom($data);
                return $app['twig']->render('financeiro/lancamento/custom_list.html.twig', [
                            'isAllowed' => $app[$this->service]->isAllowed(true),
                        'bind_path' => $this->bind,        
                        $this->param_view => $result,
                        'additional' => $app[$this->service]->infoAdditional($this->checkOwner()),
                        'fields_table' => $this->fields_table,
                        'object_key_table' => $this->object_key_table,
                        'path_table_aditional' => $this->path_table_aditional,
                        'pagination' => '',
                            "Message" => $serviceManager->getMessage(),
                            'titulo' => $this->titulo,
                            "form" => $form->createView(),
                            "route" => $serviceManager->mountArrayRoute()
                ]);
            }
            return $app['twig']->render($this->view_custom, [
                        "Message" => array(),
                        "form" => $form->createView(),
                        'titulo' => $this->titulo,
                        "route" => $serviceManager->mountArrayRoute()
            ]);
        })->bind('personalizado_listar');
        
        $this->controller->get('/display/consultas/historicodesaldos', function () use ($app) {
            $result = $app[$this->service]->historicoSaldos($this->checkOwner());
            $saldoInicialContas = $app['ContaService']->findBy(['user'=>$this->checkOwner()]);
            return $app['twig']->render('financeiro/lancamento/historico_de_saldos_list.html.twig', [
                        $this->param_view => $result, 
                        'saldoInicialContas' => $saldoInicialContas,
                        'titulo' => 'Hist&oacute;rico de saldos',
            ]);
        })->bind('historicodesaldos_listar');
        
        $this->controller->get('/display/consultas/graphccusto', function () use ($app) {  
            $base_date = new \DateTime((new \Symfony\Component\HttpFoundation\Session\Session())->get('baseDate') . "-01");
            $ccustodesc = $app['CentrocustoService']->findBy(['user'=>$this->checkOwner()]);
            foreach ($ccustodesc as $value) {
                $ccusto[] = $value->getDescricao(); 
            }
            for ($i=1; $i <= 3; $i++){
                $result[$base_date->format('m/Y')] = json_encode($app[$this->service]->getDespesasCentroCusto($this->checkOwner(),$base_date->format('Y-m-d')));
                $base_date->sub(new \DateInterval("P1M"));
            }
            
            return $app['twig']->render('financeiro/lancamento/graphccusto_list.html.twig', [
                        $this->param_view => $result, 
                        'categorias' => json_encode($ccusto), 
            ]);
        })->bind('graphccusto_listar');
    }

}
