<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheTypeEmp extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Description')
            ->add('dateDebut')
            ->add('dataFin')
            ->add('Status', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'Pending',
                    'Waiting' => 'Waiting',
                    'Competed' => 'Competed',
                ],
            ]);
            /**
             * Delete the project ==>Get the current project
             * He can add new Task in the current project
             */
//            ->add('projet', EntityType::class, [
//                'class' => Projet::class,
//                'choice_label' => function ($projet) {
//                    return $projet->getnomProjet();
//                },
//            ])
            /**
             * Listing only employees --> Find by Role !
             */

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }

}