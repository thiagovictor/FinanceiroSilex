<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use TVS\Application;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class CategoriaType extends AbstractType {

    private $centrocusto = [];
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $this->centrocusto = $this->app['CentrocustoService']->fatchPairs();

        $builder->add('centrocusto', "choice", array(
            'choices' => $this->centrocusto,
            'constraints' => array(new NotBlank()),
            'label' => 'Centro de custo',
            'placeholder' => 'Escolha um item',
            'required' => true,
                )
        )->add('descricao', "text", array(
            'constraints' => array(new NotBlank(), new Length(array('max' => 100))),
            'label' => 'Descri&ccedil;&atilde;o',
                )
        );
    }

    public function getName() {
        return 'categoria';
    }

}
