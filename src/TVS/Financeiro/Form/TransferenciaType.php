<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use TVS\Base\Validator\Constraint\InvalidDate;
use TVS\Application;

class TransferenciaType extends AbstractType {

    protected $conta;
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->conta = $this->app['ContaService']->fatchPairs();

        return $builder->add('option', 'hidden', array(
                    'data' => 'transferencia',
                        )
                )->add('valor', "money", array(
                    'currency' => 'BRL',
                    'grouping' => '1', //NAO SETAR TRUE CONFORME DOCUMENTAÇÃO SYMFONY
                    'constraints' => array(new NotBlank()),
                    'label' => 'Valor',
                        )
                )->add('descricao', "text", array(
                    'constraints' => array(new NotBlank(), new Length(array('max' => 100))),
                    'label' => 'Descri&ccedil;&atilde;o',
                        )
                )->add('vencimento', "text", array(
                    'constraints' => array(new NotBlank(),new InvalidDate()),
                    'label' => 'Data',
                        )
                )->add('conta', 'choice', array(
                    'choices' => $this->conta,
                    'required' => true,
                    'label' => 'Debitar'
                        )
                )->add('conta2', 'choice', array(
                    'choices' => $this->conta,
                    'required' => true,
                    'label' => 'Creditar'
                        )
                );
    }

    public function getName() {
        return 'TransferenciaForm';
    }

}
