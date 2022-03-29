<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
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
            ->add('username')
            ->add('firstname')
            ->add('lastname')
            ->add('country')
            ->add('description', TextType::class)
            ->add('twitterUsername')
            ->add('twitchUsername')
            ->add('redditUsername')
            ->add('youtubeUsername', TextType::class, [
                'label' => "Le nom contenu dans l'url personnalisée de votre chaine youtube (youtube.com/c/YouTubeCreators)",
                'attr' => [
                    'placeholder' => "YoutubeCreators"
                ]
            ])
            ->add('discordServerToken')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
