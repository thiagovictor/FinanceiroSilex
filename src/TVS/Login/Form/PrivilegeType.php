<?php

namespace TVS\Login\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Form\FormEvent;
//use Symfony\Component\Form\FormEvents;
use TVS\Application;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrivilegeType extends AbstractType {

    private $users = [];
    private $routes = [];
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $data = $event->getData();
//            $form = $event->getForm();
//            if ($data) {
//                $this->users = $this->app['LoginService']->fatchPairs();
//                $this->routes = $this->app['RouteService']->fatchPairs();
//            }
//            $form->add('user', "choice", array(
//                'choices' => $this->users,
//                'required' => false,
//                    )
//            );
//        });

        $this->users = $this->app['LoginService']->fatchPairs();
        $this->routes = $this->app['RouteService']->fatchPairs();

        $builder->add('user', "choice", array(
            'choices' => $this->users,
            'constraints' => array(new NotBlank()),
            'label'=>'Usu&aacute;rio',
            'placeholder' => 'Escolha um item',
            'required' => true,
                )
        )->add('route', "choice", array(
            'choices' => $this->routes,
            'label'=>'M&oacute;dulo',
            'constraints' => array(new NotBlank()),
            'placeholder' => 'Escolha um item',
            'required' => true,
                )
        )->add('display', "checkbox", [
            'label' => 'Visualizar',
            'required' => false,
                ]
        )->add('new', "checkbox", [
            'label' => 'Adicionar',
            'required' => false,
                ]
        )->add('edit', "checkbox", [
            'label' => 'Alterar',
            'required' => false,
                ]
        )->add('delete', "checkbox", [
            'label' => 'Remover',
            'required' => false,
                ]
        );
    }

    public function getName() {
        return 'privilege';
    }

}
