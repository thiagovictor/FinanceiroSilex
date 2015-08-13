<?php

namespace TVS\Login\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ConfigType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder->add('nome', "text", array(
                        'constraints' => array(new NotBlank(), new Length(array('max' => 30))),
                        'label' => 'Nome configurador',
                            )
                    )->add('parametros', "textarea", array(
                        'constraints' => array(new NotBlank()),
                        'label' => 'Par&acirc;metros',
                            )
                    );
                   
    }

    public function getName()
    {
        return 'ConfigForm';
    }
}
