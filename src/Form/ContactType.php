<?php

namespace App\Form;

use App\Entity\ContactRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => [
                    'placeholder' => "johndoe@playz.com"
                ]
            ])
            ->add('subject', TextType::class,
            [
                'attr' => [
                    'placeholder' => "L'objet de votre message",
                    'label' => 'Sujet'
                ]
                
            ])
            ->add('message', TextareaType::class,
                [
                    'attr' => [
                        'placeholder' => "Votre message",
                        'label' => 'Message',
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactRequest::class,
        ]);
    }
}
