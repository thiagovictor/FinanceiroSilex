<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;


class TipoType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        return $builder->add('descricao', "text", array(
                            'constraints' => array(new NotBlank(), new Length(array('max' => 30))),
                            'label' => 'Descri&ccedil;&atilde;o',
                                )
                        )->add('javascript', "text", array(
                        'constraints' => array(new NotBlank()),
                        'label' => 'Javascript',
                            )
                        );
                   
    }

    public function getName()
    {
        return 'TipoForm';
    }
}
