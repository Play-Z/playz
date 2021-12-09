<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Nom du jeu'
                )
            ))
            ->add('description', null, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Description du jeu...'
                )
            ))
            ->add('logo', null, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'URL du logo'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
