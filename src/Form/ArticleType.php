<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => "Titre du conseil",
                "attr" => [
                    "placeholder" => "Titre du conseil"
                ]
            ])
            ->add('content', TextareaType::class, [
                "label" => "Votre conseil",
                "attr" => [
                    "placeholder" => "Votre conseil"
                ]
            ])

            ->add('picture', UrlType::class, [
                "label" => "Votre image *",
                "attr" => [
                    "placeholder" => "Votre image"
                ],
                "help" => "* L'url d'une image"
            ])

            ->add('status', ChoiceType::class, [
                "choices" => [
                    "brouillon" => 0,
                    "publié" => 1,
                    "désactivé" => 2,
                ],
                "label" => "Statut"
            ])

            ->add('author', EntityType::class, [
                "class" => User::class,
                "label" => "Auteur",
                "attr" => [
                    "placeholder" => "Auteur",
                ]
            ])

            ->add('category', EntityType::class, [
                "class" => Category::class,
<<<<<<< HEAD
<<<<<<< HEAD
                "label" => "Catégorie",
                "multiple" => false,
            ]);
=======
                "label" => "Catégorie" ,
                "multiple" => false,     
                          
            ]) 
        ;
>>>>>>> FIX: modification of the role in UserType
=======
                "label" => "Catégorie",
                "multiple" => false,
            ]);
>>>>>>> c3b7c9e2b232ba2b63e327a802af86ad5c732c78
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}