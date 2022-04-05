<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'label' => 'Jeu principal'
            ])
            ->add('public', CheckboxType::class, [
                'label'    => 'Equipe publique ?',
                'required' => false

            ])
            ->add('emplacement', IntegerType::class, [
                'attr' => [
                    'min' => '1',
                    'max' => '10'
                ],
                'label' => "Nombre d'emplacements dans l'équipe"
            ])
            ->add('location', CountryType::class, [
                'label' => "Pays"
            ])
            ->add('description')
            ->add('twitterUsername', TextType::class, [
                'label' => "Nom d'utilisateur Twitter"
            ])
            ->add('twitchUsername', TextType::class, [
                'label' => "Nom d'utilisateur Twitch"
            ])
            ->add('RedditUsername', TextType::class, [
                'label' => "Nom d'utilisateur Reddit"
            ])
            ->add('youtubeUsername', TextType::class, [
                'label' => "Le nom contenu dans l'url personnalisée de votre chaine youtube (youtube.com/c/YouTubeCreators)",
                'attr' => [
                    'placeholder' => "YoutubeCreators"
                ]
            ])
            ->add('discordServerToken', TextType::class, [
                'label' => "Code d'invitation de votre serveur Discord"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
