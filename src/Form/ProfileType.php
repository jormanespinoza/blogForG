<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Presta\ImageBundle\Form\Type\ImageType;

class ProfileType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add('profileImageFile', ImageType::class, [
                'label' => 'Imagen de perfil',
                'help' => 'Dimensiones: min. 500x500px / Formatos: .jpg, .png, .jpeg / Max. 2048kB',
                'required' => false,
                'upload_button_class' => 'btn btn-sm btn-dark',
                'preview_height' => 'auto',
                'max_width' => 500,
                'max_height' => 500,
                'enable_remote' => false,
                'upload_mimetype' => 'image/jpeg',
                'aspect_ratios' => [] // Needs to be empty
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
