<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetTypeEditAdmin extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProjet')
            ->add('Description')
            ->add('dateDebut')
            ->add('dataFin')
//            ->add('Status', ChoiceType::class, [
//                'choices' => [
//                    'Pending' => 'Pending',
//                    'Waiting' => 'Waiting',
//                    'Competed' => 'Competed',
//                ],
//            ])
            /**
             * Make the user field unchangeble
             * Or
             * Delete the user field
             */
            ->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_PROJECT_MANAGER"%');
                },
                'choice_label' => function ($user) {
                    return $user->getFirstname();
                },
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}