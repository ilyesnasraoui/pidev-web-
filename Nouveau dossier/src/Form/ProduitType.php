<?php

namespace App\Form;

use App\Entity\Produit;
use App\Repository\CategorieProduitRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;




class ProduitType extends AbstractType
{



    private $cRepository;
    public  function __construct(CategorieProduitRepository $cRepository)
    {
        $this->cRepository=$cRepository;

    }




    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('couleur')
            ->add('nomProduit')
            ->add('prix')
            ->add('image',FileType::class,[
                'required'=>false,
                'mapped'=>false])
            ->add('description')
            ->add('idCategorie',ChoiceType::class,[

                'multiple' => false,
                'choices' =>
                    $this->cRepository->createQueryBuilder('u')->select('u.idCategorie')->getQuery()->getResult(),
                'choice_label' => function ($choice) {
                    return $choice;
                },]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
