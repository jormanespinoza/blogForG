<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RegisterType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
                'attr' => [
                    'autofocus' => true
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Apellido',
                'required' => true
            ])
            ->add('username', TextType::class, [
                'label' => 'Nombre de usuario',
                'required' => true
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => true
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Asegurate de que la contraseña coincida.',
                'first_options'  => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Repetir contraseña']
            ))
            ->add('isActive', HiddenType::class, array(
                'empty_data' => true
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
