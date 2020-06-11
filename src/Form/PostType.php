<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Presta\ImageBundle\Form\Type\ImageType;

class PostType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Título',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'class' => 'slugable-input',
                    'data-slug' => 'url'
                ]
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Contenido',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'class' => 'applyCKEditor'
                ]
            ])
            ->add('mainImageFile', ImageType::class, [
                'label' => 'Imagen principal',
                'help' => 'Dimensiones: min. 1920x1080px / Formatos: .jpg, .png, .jpeg / Max. 2048kB',
                'required' => false,
                'upload_button_class' => 'btn btn-sm btn-dark',
                'preview_height' => 'auto',
                'max_width' => 1920,
                'max_height' => 1080,
                'enable_remote' => false,
                'upload_mimetype' => 'image/jpeg',
                'aspect_ratios' => [] // Needs to be empty
            ])
            ->add('url', TextType::class, [
                'label' => 'Enlace'
            ])
            ->add('visible', CheckboxType::class, [
                'label' => 'Visible',
                'data' => true,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
