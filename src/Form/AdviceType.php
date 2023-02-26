<?php

namespace App\Form;

use App\Entity\Advice;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => "Titre",
                "attr" => [
                    "placeholder" => "Titre du conseil"
                ]
            ])

            ->add('content', CKEditorType::class, [
                "label" => "Conseil",
                "config" => [
                    "uiColor" => "#ffffff",
                    "toolbar" => "basic",
                ],
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
            'data_class' => Advice::class,
        ]);
    }
}
