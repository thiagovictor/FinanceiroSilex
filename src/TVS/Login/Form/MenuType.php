<?php

namespace TVS\Login\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class MenuType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder->add('label', "text", array(
                        'constraints' => array(new NotBlank(), new Length(array('max' => 30))),
                        'label' => 'Menu',
                            )
                    )->add('desc', "text", array(
                        'constraints' => array(new NotBlank(), new Length(array('max' => 50))),
                        'label' => 'Descri&ccedil;&atilde;o',
                            )
                    );
                   
    }

    public function getName()
    {
        return 'MenuForm';
    }
}
