<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTeamType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $team = $builder->getData();

        $builder
            ->add('name')
            ->add('public')
            ->add('emplacement', IntegerType::class, [
                'attr' => [
                    'min' => count($team->getUsers()),
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
