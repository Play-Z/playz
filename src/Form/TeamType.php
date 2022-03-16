<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\UserRelationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TeamType extends AbstractType
{
    private $userRelationRepository;
    private $security;

    public function __construct(UserRelationRepository $userRelationRepository, Security $security)
    {
        $this->userRelationRepository = $userRelationRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('users', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class,
                'choices' => $this->userRelationRepository->findAllFriends($this->security->getUser()),

                // uses the User.username property as the visible option string
                'choice_label' => 'username',

                // used to render a select box, check boxes or radios
                 'multiple' => false,
                 'expanded' => false,
            ])
            ->add('public')
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
