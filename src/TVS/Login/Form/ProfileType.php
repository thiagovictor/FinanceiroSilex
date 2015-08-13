<?php

namespace TVS\Login\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;

class ProfileType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        return $builder->add('username', "text", [
                    'read_only'=>true,
                    'label' => 'Usu&aacute;rio',
                    'constraints' => [new NotBlank()],
                        ]
                )->add('email', "email", [
                    'constraints' => [new NotBlank(), new Email()],
                        ]
                )->add('password', "password", [
                    'label' => 'Senha',
                    'required' => false,
                        ]
                )->add('ativo', "hidden", [
                    'label' => 'Ativo?',
                    'required' => true,
                        ]
                )->add('image', 'file',[
                    'label' => 'Imagem',
                    'required' => false,
                    'constraints' => [new Image(['mimeTypes' => ['image/jpeg', 'image/jpg']])],
                ]);
    }

    public function getName() {
        return 'ProfileForm';
    }

}
