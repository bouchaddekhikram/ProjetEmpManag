<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tache;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
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
            ])
            ->add('projet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => function ($projet) {
                    return $projet->getnomProjet();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
