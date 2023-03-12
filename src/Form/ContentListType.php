<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Title',
                'attr' => [
                    'placeholder' => 'Title'
                ]
            ])
            ->add('content', TextType::class, [
                'required' => false,
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Contenu'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'Statut de publication',
                'choices' => [
                    'Tous' => null,
                    'Brouillon' => 0,
                    'Publié' => 1,
                    'Archivé' => 2
                ]
            ])
            ->add('user', EntityType::class, [
                'required' => false,
                'label' => 'Utilisateur',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNickname() . ' (' . $user->getFirstname() . ' ' . $user->getLastname() . ')';
                },
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->orderBy('u.nickname', 'ASC');
                }
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                }
            ])
            ->add('sortType', ChoiceType::class, [
                'required' => true,
                'label' => 'Trier par',
                'choices' => [
                    'Date de création' => null,
                    'Titre' => 'title',
                    'Contenu' => 'content',
                    'Statut de publication' => 'status',
                    'Utilisateur' => 'user',
                    'Catégorie' => 'category'
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
