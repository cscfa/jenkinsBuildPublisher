<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Build;
use AppBundle\DTO\ProjectDto;
use Doctrine\ORM\EntityRepository;

class ProjectForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add(
                'builds',
                EntityType::class,
                array(
                    'required'=>false,
                    'class'=>Build::class,
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function(EntityRepository $repository){
                        $builder = $repository->createQueryBuilder('b');
                        $builder->where($builder->expr()->isNull('b.project'));

                        return $builder;
                    }
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', ProjectDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}
