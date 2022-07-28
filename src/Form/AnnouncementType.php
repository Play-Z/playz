<?php

namespace App\Form;

use App\Entity\Announcement;
use App\Entity\Team;
use App\Entity\Tournament;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('image', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => true,
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
            ])
            ->add('teamAnnouncement', EntityType::class, [
                "class" => Team::class,
                "choice_label" => "name",
                "label" => "Equipe",
                "placeholder" => "Selectionner une équipe"
            ])
            ->add('tournamentAnnouncement' , EntityType::class, [
                "class" => Tournament::class,
                "choice_label" => "name",
                "label" => "Tournoi",
                "placeholder" => "Selectionner un tournoi"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
        ]);
    }
}
