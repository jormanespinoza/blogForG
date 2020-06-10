<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Presta\ImageBundle\Form\Type\ImageType;

class UserType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Nombre',
                'required' => true
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
            ->add('profileImageFile', ImageType::class, [
                'label' => 'Imagen de Perfil',
                'help' => 'Dimensiones: min. 250x250px / Formatos: .jpg, .png, .jpeg / Max. 2048kB',
                'required' => false,
                'upload_button_class' => 'btn btn-default js-btn-cropper',
                'preview_height' => 'auto',
                'enable_remote' => false,
                'upload_mimetype' => 'image/jpeg',
                'aspect_ratios' => [] // Needs to be empty
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
