<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
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
                    'class' => 'only-alphabetic-chars'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Apellido',
                'required' => true,
                'attr' => [
                    'class' => 'only-alphabetic-chars'
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Nombre de usuario',
                'required' => true
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => true
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
