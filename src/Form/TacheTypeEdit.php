<?php

namespace App\Form;
use App\Entity\User;
use App\Entity\Projet;
use App\Entity\Tache;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheTypeEdit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
//            ->add('projet', EntityType::class, [
//                'class' => Projet::class,
//                'choice_label' => function ($projet) {
//                    return $projet->getnomProjet();
//                },
//            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_EMPLOYEE"%');
                },
                'choice_label' => function ($user) {
                    return $user->getFirstname();
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
