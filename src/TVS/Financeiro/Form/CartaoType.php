<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;


class CartaoType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        return $builder->add('descricao', "text", array(
                            'constraints' => array(new NotBlank(), new Length(array('max' => 100))),
                            'label' => 'Descri&ccedil;&atilde;o',
                                )
                        )->add('vencimento', "text", array(
                            'constraints' => array(new NotBlank(), new Length(array('min'=>2,'max' => 2))),
                            'label' => 'Dia de vencimento',
                                )
                        );
                   
    }

    public function getName()
    {
        return 'CartaoForm';
    }
}
