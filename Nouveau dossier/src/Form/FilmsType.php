<?php

namespace App\Form;

use App\Entity\Films;
use App\Entity\CategorieFilm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class FilmsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('idCategorie',EntityType::class,['class'=>CategorieFilm::class,
                'choice_label'=>'idCategorie'])
            ->add('language')
            ->add('nomFilm')
            ->add('dureeFilm')
         ->add('image', FileType::class,[ 'mapped'=> false])

            ->add('description')
            ->add('utube')
            ->add('rated')
            ->add('date')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Films::class,
        ]);
    }
}
