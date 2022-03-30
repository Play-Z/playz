<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Tournament;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EditTournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logo', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => true,
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true
            ])
            ->add('name')
            ->add('description')
            ->add('max_team_participant',ChoiceType::class, [
                'choices'=> [
                    4=>4,
                    8=>8,
                    16=>16,
                    32=>32,
                    64=>64
                ]
            ])
            ->add('max_team_players',ChoiceType::class, [
                'choices'=> [
                    1=>1,
                    2=>2,
                    3=>3,
                    5=>5
                ]
            ])
            ->add('game', EntityType::class, [
                // looks for choices from this entity
                'class' => Game::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('startAt')
            ->add('startInscriptionAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
        ]);
    }
}
