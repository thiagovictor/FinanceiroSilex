<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ContaType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        return $builder->add('descricao', "text", array(
                    'constraints' => array(new NotBlank(), new Length(array('max' => 100))),
                    'label' => 'Descri&ccedil;&atilde;o',
                        )
                )->add('logo', "choice", array(
                    'choices' => array(
                        '/img/padrao.png' => 'Pad&atilde;o',
                        '/img/alelo.png' => 'Alelo',
                        '/img/caixa.png' => 'Caixa Econ&ocirc;mica',
                        '/img/bradesco.jpg' => 'Bradesco' ,
                        '/img/itau.jpg' => 'Ita&uacute;' ,
                        '/img/santander.png' => 'Santander',
                        '/img/bb.png' => 'Banco do Bransil' ,
                        '/img/mercantil.jpg' => 'Mercantil',
                        '/img/dinheiro.jpg' => 'Dinheiro',
                    ),
                    'label' => 'Logo',
                        )
                )->add('saldo', "money", array(
                    'currency' => 'BRL',
                    'grouping' => '1', //NAO SETAR TRUE CONFORME DOCUMENTAÇÃO SYMFONY
                    'constraints' => array(new NotBlank()),
                    'label' => 'Saldo',
                        )
                )->add('ativo', "checkbox", [
                    'label' => 'Ativa?',
                    'required' => false,
                        ]
        );
    }

    public function getName() {
        return 'ContaForm';
    }

}
