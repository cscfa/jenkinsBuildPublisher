<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Build;
use AppBundle\DTO\BuildFileDto;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class BuildFileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('contentType', TextType::class)
            ->add('content', TextareaType::class)
            ->add(
                'build',
                EntityType::class,
                array(
                    'required'=>false,
                    'class'=>Build::class
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', BuildFileDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}
