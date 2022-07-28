<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Tournament;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TournamentType extends AbstractType
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
            ->add('name', TextType::class, [
                'label' => "Nom d'utilisateur"
            ])
            ->add('PouleType',CheckboxType::class,[
                'label'=>'Créer un tournoi avec poules',
                'required'=>false
            ])
            ->add('description')
            ->add('max_team_participant',ChoiceType::class, [
                'label' => "Nombre d'équipes dans le tournoi",
                'choices'=> [
                    4=>4,
                    8=>8,
                    16=>16,
                    32=>32,
                    64=>64
                ]
            ])
            ->add('max_team_players',ChoiceType::class, [
                'label' => "Nombre de joueurs par équipes",
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
                'label' => 'Jeu du tournoi'
            ])
            ->add('startAt', DateTimeType::class,[
                'widget' => 'single_text',
                'label'=>'Tournament start at'
            ])
            ->add('startInscriptionAt', DateTimeType::class,[
                'widget' => 'single_text',
                'label'=>'Tournament inscription start at'
            ])
            ->add('priceFirst', TextType::class,[
                'label'=>'Récompense 1er'
            ])
            ->add('priceSecond', TextType::class,[
                'label'=>'Récompense 2ème'
            ])
            ->add('priceThird', TextType::class,[
                'label'=>'Récompense 3ème'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
        ]);
    }
}
