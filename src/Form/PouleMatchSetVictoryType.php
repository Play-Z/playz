<?php

namespace App\Form;

use App\Entity\PouleMatch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PouleMatchSetVictoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_team_win',ChoiceType::class,[
                'choices'=> [
                    'Team 1' => true ,
                    'Team 2' => false
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PouleMatch::class,
        ]);
    }
}
