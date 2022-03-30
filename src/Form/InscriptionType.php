<?php

namespace App\Form;


use App\Entity\Team;
use App\Entity\TournamentTeam;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class InscriptionType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $team = $options['team'] ;

        $builder
            ->add('players', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class ,
                'choices' => $team,

                // uses the User.username property as the visible option string
                'choice_label' => 'username',

                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => true,
                //'data'=>$team

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TournamentTeam::class,
            'team' => Team::class
        ]);
    }
}
