<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;


class PeriodoType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        return $builder->add('descricao', "text", array(
                            'constraints' => array(new NotBlank(), new Length(array('max' => 30))),
                            'label' => 'Descri&ccedil;&atilde;o',
                                )
                        )->add('incremento', "text", array(
                            'constraints' => array(new Length(array('max' => 2))),
                            'label' => 'Incremento de meses',
                                )
                        );
                   
    }

    public function getName()
    {
        return 'PeriodoForm';
    }
}
