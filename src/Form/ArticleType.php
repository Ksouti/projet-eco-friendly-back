<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                "label" => "Titre",
                "attr" => [
                    "placeholder" => "Titre de l'article"
                ]
            ])

            ->add('content', CKEditorType::class, [
                "label" => "Article",
                "attr" => [
                    "placeholder" => "Contenu de l'article"
                ],
                "config" => [
                    "uiColor" => "#eeeeee",
                    "toolbar" => "basic",
                ],
            ])

            ->add('picture', UrlType::class, [
                "label" => "Image d'illustration*",
                "attr" => [
                    "placeholder" => "Votre image"
                ],
                "help" => "* L'url d'une image"
            ])

            ->add('status', ChoiceType::class, [
                "choices" => [
                    "Brouillon" => 0,
                    "Publié" => 1,
                ],
                "label" => "Statut"
            ])

            ->add('category', EntityType::class, [
                "class" => Category::class,
                "label" => "Catégorie",
                "multiple" => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
