<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => true,
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
            ])
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur"
            ])
            ->add('firstname', TextType::class, [
                'label' => "Prénom"
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom de famille"
            ])
            ->add('country', CountryType::class, [
                'label' => "Pays"
            ])
            ->add('age', IntegerType::class, [
                'attr' => [
                    'min' => '12',
                    'max' => '100'
                ],
                'label' => "Age"
            ])
            ->add('gender', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
                'label' => 'Genre :'
            ])
            ->add('description', TextType::class)
            ->add('twitterUsername', TextType::class, [
                'label' => "Nom d'utilisateur Twitter"
            ])
            ->add('twitchUsername', TextType::class, [
                'label' => "Nom d'utilisateur Twitch"
            ])
            ->add('redditUsername', TextType::class, [
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
            'data_class' => User::class,
        ]);
    }
}
