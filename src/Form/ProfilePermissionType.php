<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfilePermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => array(
                    'usuario' => 'ROLE_USER',
                    'administrador' => 'ROLE_ADMIN',
                    'super-administrador' => 'ROLE_SUPER_ADMIN',
                ),
                'multiple' => true
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Asegurate de que la contraseña coincida.',
                'first_options'  => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Confirmar contraseña']
            ))
            ->add('isActive', CheckboxType::class, [
                'label' => 'Usuario activo',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
