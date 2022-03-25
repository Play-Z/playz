<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EditTeamType extends AbstractType
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
            ->add('game', EntityType::class, [
                // looks for choices from this entity
                'class' => Game::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('public')
            ->add('emplacement', IntegerType::class, [
                'attr' => [
                    'min' => '1',
                    'max' => '10'
                ],
            ])
            ->add('description')
            ->add('logo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
