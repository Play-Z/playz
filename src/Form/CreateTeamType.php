<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Team;
use App\Repository\UserRelationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CreateTeamType extends AbstractType
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
        $friendsRelation = $this->userRelationRepository->findAllFriendsOfUser($currentUser);
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
            ->add('logo', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => true,
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true
            ])
            ->add('name')
            ->add('user', ChoiceType::class, [
                // looks for choices from this entity
                'choices' => $friends,

                // uses the User.username property as the visible option string
                'choice_label' => 'username',

                // used to render a select box, check boxes or radios
                 'multiple' => true,
                 'expanded' => true,
                 'data' => $friends,
            ])
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
