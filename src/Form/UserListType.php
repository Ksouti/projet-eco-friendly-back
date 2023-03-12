<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'required' => false,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('firstname', TextType::class, [
                'required' => false,
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'required' => false,
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('nickname', TextType::class, [
                'required' => false,
                'label' => 'Pseudo',
                'attr' => [
                    'placeholder' => 'Pseudo'
                ]
            ])
            ->add('code', TextType::class, [
                'required' => false,
                'label' => 'Code',
                'attr' => [
                    'placeholder' => 'Code'
                ]
            ])
            ->add('is_verified', ChoiceType::class, [
                'required' => false,
                'label' => 'Statut compte',
                'choices' => [
                    'Tous' => null,
                    'Vérifié' => 1,
                    'Non vérifié' => 0
                ]
            ])
            ->add('is_active', ChoiceType::class, [
                'required' => false,
                'label' => 'Statut activité',
                'choices' => [
                    'Tous' => null,
                    'Actif' => 1,
                    'Inactif' => 0
                ]
            ])
            ->add('sortType', ChoiceType::class, [
                'required' => true,
                'label' => 'Trier par',
                'choices' => [
                    'Date de création' => null,
                    'Email' => 'email',
                    'Prénom' => 'firstname',
                    'Nom' => 'lastname',
                    'Pseudo' => 'nickname',
                    'Code' => 'code',
                    'Statut compte' => 'is_verified',
                    'Statut activité' => 'is_active'
                ]
            ])
            ->add('sortOrder', ChoiceType::class, [
                'required' => true,
                'label' => 'Ordre',
                'choices' => [
                    'Décroissant' => null,
                    'Croissant' => 'ASC'
                ]
            ])
            ->add('dateFrom', DateTimeType::class, [
                'required' => false,
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'type' => 'datetime-local'
                ]
            ])
            ->add('dateTo', DateTimeType::class, [
                'required' => false,
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'type' => 'datetime-local'
                ]
            ]);
    }
}
