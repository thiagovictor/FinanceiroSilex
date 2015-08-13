<?php

namespace TVS\Login\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use TVS\Application;

class RouteType extends AbstractType 
{
    protected $menus;
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $this->menus = $this->app['MenuService']->fatchPairs();
        return $builder->add('route', "text", array(
                        'constraints' => array(new NotBlank(), new Length(array('max' => 30))),
                        'label' => 'M&oacute;dulo',
                            )
                        )->add('label', "text", array(
                            'constraints' => array(new NotBlank(), new Length(array('max' => 30))),
                            'label' => 'Descri&ccedil;&atilde;o',
                                )
                        )->add('menu', "choice", array(
                            'choices' => $this->menus,
                            'placeholder' => 'Escolha um item',
                            'required' => false,
                        ));
                   
    }

    public function getName()
    {
        return 'RouteForm';
    }
}
