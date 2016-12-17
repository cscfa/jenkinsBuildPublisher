<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Project;
use AppBundle\Entity\Status;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\DTO\BuildDto;
use AppBundle\Entity\BuildFile;
use Doctrine\ORM\EntityRepository;

class BuildForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add(
                'project',
                EntityType::class,
                array('class'=>Project::class)
            )->add(
                'status',
                EntityType::class,
                array('class'=>Status::class)
            )->add(
                'files',
                EntityType::class,
                array(
                    'required'=>false,
                    'class'=>BuildFile::class,
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function(EntityRepository $repository){
                        $builder = $repository->createQueryBuilder('bf');
                        $builder->where($builder->expr()->isNull('bf.build'));

                        return $builder;
                    }
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', BuildDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}
