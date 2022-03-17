<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\UserRelationRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        $currentUser = $this->security->getUser();
        $friendsRelation = $this->userRelationRepository->findAllFriends($currentUser);
        $friends = [];

        foreach ($friendsRelation as $userRelation){
            if($userRelation->getSender() !== $currentUser){
                $friends[] = $userRelation->getSender();
            }
            elseif($userRelation->getRecipient() !== $currentUser){
                $friends[] = $userRelation->getRecipient();
            }
        }

        $builder
            ->add('name')
            ->add('users', ChoiceType::class, [
                // looks for choices from this entity
                'choices' => $friends,

                // uses the User.username property as the visible option string
                'choice_label' => 'username',

                // used to render a select box, check boxes or radios
                 'multiple' => true,
                 'expanded' => true,
                 'data' => $friends,
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
