<?php

namespace App\Form;


use App\Entity\Reservation;
use App\Entity\Users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {$choice = array();
     $choice["admin"]="admin";
     $choice["client"]="client";
        $builder
            ->add('username')
            ->add('password')
            ->add('email')
            ->add('idSalle')
            ->add('fname')
            ->add('lname')
            ->add('idcard')
            ->add('phone')

            ->add('role',ChoiceType::class,
                array('choices'=>$choice))
            ->add('blocked')
           ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
