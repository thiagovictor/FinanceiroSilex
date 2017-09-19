<?php

namespace TVS\Login\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use TVS\Base\Lib\RepositoryFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfileController implements ControllerProviderInterface {

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
    protected $is_owner = false;
    
    
    protected function checkOwner() {
        if ($this->is_owner) {
            return (new Session())->get('user');
        }
        return false;
    }
    
    public function __construct() {
        $this->registros_por_pagina = 5;
        $this->service = 'LoginService';
        $this->bind = 'profile';
        $this->param_view = 'result';
        $this->view_new = 'login/default/default_new.twig';
        $this->view_edit = 'login/profile/profile_edit.twig';
        $this->view_list = 'template.twig';
        $this->form = 'ProfileForm';
        $this->titulo = "Perfil do usu&aacute;rio";
    }

    public function connect(Application $app) {
        $this->controller = $app['controllers_factory'];
        $this->app = $app;

        //####EDITANDO REGISTRO#######
        $this->controller->match('/edit', function () use ($app) {
            $serviceManager = $app[$this->service];
            $form = $app[$this->form];

            if ($this->form_edit) {
                $form = $app[$this->form_edit];
            }

            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $data["id"] = $app['session']->get('user')->getId();
                $serviceManager->update($data,$this->checkOwner());
                return $app->redirect($app["url_generator"]->generate('inicio_listar'));
            }
            $result = $serviceManager->find($app['session']->get('user')->getId());
            $form->setData($result->toArray());
            return $app['twig']->render($this->view_edit, [
                        "form" => $form->createView(),
                        'titulo' => $this->titulo,
                        "Message" => $serviceManager->getMessage(),
                        "route" => $serviceManager->mountArrayRoute()
            ]);
        })->bind($this->bind . '_edit');
        
        $this->controller->get('/display/preferenceimage/{id}', function ($id) use ($app) {
            $user = $app['LoginService']->find($id);
            $image  = $user->getImage();
            if(!$user->getImage()){
                $image = '/default.jpg';
            }
            return new Response(
                    (new RepositoryFile("../data".$image))->getArquivo(), 200, array(
                        'Content-Type' => 'image/jpg',
                        'Content-Disposition' => 'filename="image.jpg"'
                    )
            );   
        })->bind('PreferenceImage');


        return $this->controller;
    }

}
