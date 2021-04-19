<?php

namespace App\Form;

use App\Entity\Planning;
use App\Entity\Films;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idFilm',EntityType::class,['class'=>Films::class,
                'choice_label'=>'idfilm'])
            ->add('idSalle')
            ->add('day')
            ->add('month')
            ->add('year')
            ->add('projectionTime')
            ->add('places')
            ->add('date')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}
