<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\UserRelationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $friends = $options['friends'];

        if (!empty($friends)){
            $builder->add('users', EntityType::class, [
                'class' => User::class ,
                'choices' => $friends,

                // uses the User.username property as the visible option string
                'choice_label' => 'username',

                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => true,
                'label' => 'Amis à inviter dans votre équipe'
            ]);
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
            ->add('name', TextType::class, [
                'label' => "Nom d'équipe"
            ])
            ->add('game', EntityType::class, [
                // looks for choices from this entity
                'class' => Game::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                'multiple' => false,
                'expanded' => false,
                'label' => "Jeu principal"
            ])
            ->add('public', CheckboxType::class, [
                'label'    => 'Equipe publique ?'
            ])
            ->add('emplacement', IntegerType::class, [
                'attr' => [
                    'min' => '1',
                    'max' => '10'
                ],
                'label' => "Nombre d'emplacements dans l'équipe"
            ])
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
            'friends' => User::class,
        ]);
    }
}
