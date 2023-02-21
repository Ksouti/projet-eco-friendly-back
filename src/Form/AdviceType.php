<?php

namespace App\Form;

use App\Entity\Advice;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                "label" => "Titre du conseil",
                "attr" => [
                    "placeholder" => "Titre du conseil"
                ]
            ])
            ->add('content',TextareaType::class,[
                "label" => "Votre conseil",
                "attr"=>[
                    "placeholder" => "Votre conseil"
                ]
            ])
                
            ->add('status',ChoiceType::class,[
                "choices" => [
                    "brouillon" => 0,
                    "publié" => 1,
                    "désactivé" => 2,
            ],
                "label" => "Statut"
            ])
            
            ->add('contributor',EntityType::class,[
                "class" => User ::class,
                "label" => "Ecrit par",
                "attr"=>[
                    "placeholder" => "Ecrit par",
            ]
            ])
            
            ->add('category',EntityType::class,[
                "class" => Category::class,
                "label" => "Catégorie" ,
                "multiple" => false,     
                //"expended" => true,           

            ])     
        ;       
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advice::class,
        ]);
    }
}
