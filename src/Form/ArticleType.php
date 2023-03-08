<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\DataTransformer\ImageUrlToFileTransformer;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
                    "toolbar" => "standard",
                ],
            ])

            ->add('picture', FileType::class, [
                "label" => "Image d'illustration",
                "constraints" => [
                    new File([
                        "maxSize" => "2048k",
                        "mimeTypes" => [
                            "image/jpeg",
                            "image/png",
                            "image/gif",
                        ],
                        "mimeTypesMessage" => "Veuillez uploader une image valide",
                    ])
                ],
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

        $builder->get('picture')->addModelTransformer(new ImageUrlToFileTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
