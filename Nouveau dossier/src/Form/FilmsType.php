<?php

namespace App\Form;

use App\Entity\Films;
use App\Entity\CategorieFilm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class FilmsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {$choice = array();
        $choice["romance"]="2";
        $choice["thriller"]="1";
        $choice["Action"]="3";
        $choice["Horror"]="4";
        $builder
            ->add('idCategorie',ChoiceType::class,
                array('choices'=>$choice))
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
