<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserRelation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('relatingUser', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.username', 'ASC');
                },

                // uses the User.username property as the visible option string
                'choice_label' => 'username',

                // used to render a select box, check boxes or radios
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('relatedUser', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC');
                },

                // uses the User.username property as the visible option string
                'choice_label' => 'username',

                // used to render a select box, check boxes or radios
                'multiple' => false,
                'expanded' => false,
                'allow_add' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRelation::class,
        ]);
    }
}
