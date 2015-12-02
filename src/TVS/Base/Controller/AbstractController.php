<?php

namespace TVS\Base\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Session\Session;

class AbstractController implements ControllerProviderInterface {

    protected $registros_por_pagina = 5;
    protected $service;
    protected $form_edit = null;
    protected $form;
    protected $view_new;
    protected $view_edit;
    protected $view_list;
    protected $bind;
    protected $param_view;
    protected $controller;
    protected $titulo;
    protected $app;
    protected $field_search;
    protected $path_table_aditional = [];
    protected $fields_table = [];
    protected $object_key_table = [];
    protected $is_owner = false;
    protected $multiple_forms = [];

    protected function connect_extra() {
        
    }

    protected function checkOwner() {
        if ($this->is_owner) {
            return (new Session())->get('user');
        }
        return false;
    }

    public function connect(Application $app) {
        $this->controller = $app['controllers_factory'];
        $this->app = $app;

        $this->connect_extra();

        //####LISTAGEM INICIAL#######
        $this->controller->get('/', function () use ($app) {
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
        })->bind($this->bind . '_listar');

        //####LISTAGEM BUSCA#######
        $this->controller->post('/display/search', function () use ($app) {
            $result = $app[$this->service]->findSearch(0, $this->registros_por_pagina, $app['request']->get('search'), $this->field_search, $this->checkOwner());
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
                        'search' => $app['request']->get('search'),
                        'pagination' => $app[$this->service]->pagination(1, $this->registros_por_pagina, $app['request']->get('search'), $this->field_search, $this->checkOwner())
            ]);
        })->bind($this->bind . '_search');

        //####LISTAGEM BUSCA#######
        $this->controller->get('/display/search/{page}/{search}', function ($page, $search) use ($app) {
            if ($page < 1 or $page > ceil($app[$this->service]->getRows($search, $this->field_search, $this->checkOwner()) / $this->registros_por_pagina)) {
                $page = 1;
            }
            $result = $app[$this->service]->findSearch((($page - 1) * $this->registros_por_pagina), $this->registros_por_pagina, $search, $this->field_search, $this->checkOwner());
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
                        'search' => $search,
                        'pagination' => $app[$this->service]->pagination($page, $this->registros_por_pagina, $search, $this->field_search, $this->checkOwner())
            ]);
        })->bind($this->bind . '_listar_pagination_search');


        //####LISTAGEM PAGINADA#######
        $this->controller->get('/page/{page}', function ($page) use ($app) {
            if ($page < 1 or $page > ceil($app[$this->service]->getRows(false, false, $this->checkOwner()) / $this->registros_por_pagina)) {
                $page = 1;
            }
            $result = $app[$this->service]->findPagination((($page - 1) * $this->registros_por_pagina), $this->registros_por_pagina, $this->checkOwner());
            return $app['twig']->render($this->view_list, [
                        $this->param_view => $result,
                        'isAllowed' => $app[$this->service]->isAllowed(true),
                        'bind_path' => $this->bind,
                        'path_table_aditional' => $this->path_table_aditional,
                        'fields_table' => $this->fields_table,
                        'object_key_table' => $this->object_key_table,
                        'page_atual' => $page,
                        'additional' => $app[$this->service]->infoAdditional($this->checkOwner()),
                        'titulo' => $this->titulo,
                        'pagination' => $app[$this->service]->pagination($page, $this->registros_por_pagina, false, false, $this->checkOwner())
            ]);
        })->bind($this->bind . '_listar_pagination');
        //####NOVO REGISTRO#######
        $this->controller->match('/new', function () use ($app) {
            $form = $app[$this->form];
            $form->handleRequest($app['request']);
            $serviceManager = $app[$this->service];
            if ($form->isValid()) {
                $data = $form->getData();
                $result = $serviceManager->insert($data);
                return $app['twig']->render($this->view_new, [
                            "success" => $result,
                            "Message" => $serviceManager->getMessage(),
                            'titulo' => $this->titulo,
                            "form" => $form->createView(),
                            "route" => $serviceManager->mountArrayRoute()
                ]);
            }
            return $app['twig']->render($this->view_new, [
                        "Message" => array(),
                        "form" => $form->createView(),
                        'titulo' => $this->titulo,
                        "route" => $serviceManager->mountArrayRoute()
            ]);
        })->bind($this->bind . '_new');
        
        //####NOVO REGISTRO PASSANDO PARAMETROS#######
        $this->controller->match('/new/{options}', function ($options) use ($app) {
            if(!isset($this->multiple_forms[$options])){
                $this->multiple_forms[$options] = $this->form;
            }
            if(!isset($app[$this->multiple_forms[$options]])){
                $this->multiple_forms[$options] = $this->form;
            }
            $form = $app[$this->multiple_forms[$options]];
            $form->handleRequest($app['request']);
            $serviceManager = $app[$this->service];
            if ($form->isValid()) {
                $data = $form->getData();
                $result = $serviceManager->insert($data);
                return $app['twig']->render($this->view_new, [
                            "success" => $result,
                            "Message" => $serviceManager->getMessage(),
                            'titulo' => $this->titulo,
                            "form" => $form->createView(),
                            "route" => $serviceManager->mountArrayRoute()
                ]);
            }
            return $app['twig']->render($this->view_new, [
                        "Message" => array(),
                        "form" => $form->createView(),
                        'titulo' => $this->titulo,
                        "route" => $serviceManager->mountArrayRoute()
            ]);
        })->bind($this->bind . '_new_options');

        //####EDITANDO REGISTRO#######
        $this->controller->match('/edit/{id}', function ($id) use ($app) {
            $serviceManager = $app[$this->service];
            $form = $app[$this->form];

            if ($this->form_edit) {
                $form = $app[$this->form_edit];
            }

            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $data["id"] = $id;
                $serviceManager->update($data, $this->checkOwner());
                //return $app->redirect($app["url_generator"]->generate($this->bind . '_listar'));
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
                            'Message' => $serviceManager->getMessage(),
                            'titulo' => $this->titulo,
                            'pagination' => $app[$this->service]->pagination(1, $this->registros_por_pagina, false, false, $this->checkOwner())
                ]);
            }
            
            if ($form->isSubmitted()) {
                return $app['twig']->render($this->view_edit, [
                            "form" => $form->createView(),
                            'titulo' => $this->titulo,
                            "Message" => $serviceManager->getMessage(),
                            "route" => $serviceManager->mountArrayRoute()
                ]);
            }

            $result = $serviceManager->find($id, $this->checkOwner());
            if (!$result) {
                return $app->redirect($app["url_generator"]->generate($this->bind . '_listar'));
            }
            $form->setData($result->toArray());
            return $app['twig']->render($this->view_edit, [
                        "form" => $form->createView(),
                        'titulo' => $this->titulo,
                        "Message" => $serviceManager->getMessage(),
                        "route" => $serviceManager->mountArrayRoute()
            ]);
        })->bind($this->bind . '_edit');

        //####REMOVE REGISTRO#######
        $this->controller->get('/delete/{id}', function ($id) use ($app) {
            $serviceManager = $app[$this->service];
            $serviceManager->delete($id, $this->checkOwner());
            //return $app->redirect($app["url_generator"]->generate($this->bind . '_listar'));
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
                        'Message' => $serviceManager->getMessage(),
                        'titulo' => $this->titulo,
                        'pagination' => $app[$this->service]->pagination(1, $this->registros_por_pagina, false, false, $this->checkOwner())
            ]);
        })->bind($this->bind . '_delete');

        return $this->controller;
    }

}
