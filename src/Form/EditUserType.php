<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\CallbackTransformer;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EditUserType extends AbstractType
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
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Membre' => 'ROLE_USER',
                    'Manageur de tournois' => 'ROLE_TOURNAMENT_MANAGER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'label' => 'Role :'
            ])
            ->add('firstname', TextType::class, [
                'label' => "Prénom"
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom"
            ])
            ->add('country', TextType::class, [
                'label' => "Pays"
            ])
            ->add('description')
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
                'label' => "Le nom contenu dans l'url personnalisée de la chaine youtube (youtube.com/c/YouTubeCreators)",
                'attr' => [
                    'placeholder' => "YoutubeCreators"
                ]
            ])
            ->add('discordServerToken', TextType::class, [
                'label' => "Code d'invitation de votre serveur Discord"
            ])
        ;
            
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ))
        ;

       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
