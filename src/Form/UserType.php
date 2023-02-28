<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("firstname", TextType::class, [
                "label" => "Prénom",
                "attr" => [
                    "placeholder" => "Entrez votre prénom"
                ]
            ])

            ->add("lastname", TextType::class, [
                "label" => "Nom",
                "attr" => [
                    "placeholder" => "Entrez votre nom de famille"
                ]
            ])

            ->add("nickname", TextType::class, [
                "label" => 'Pseudo',
                "attr" => [
                    "placeholder" => "Entrez votre pseudo"
                ]
            ])

            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Email"
                ]
            ])

            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "Auteur" => "ROLE_AUTHOR",
                    "Admin" =>  "ROLE_ADMIN",
                    "Utilisateur" => "ROLE_USER"
                ],
                "expanded" => true,
                "multiple" => true,
                "choice_attr" => [
                    // Set the "disabled" attribute only for the ROLE_USER checkbox
                    "Utilisateur" => ["disabled" => true],
                ],
            ])

            ->add('avatar', UrlType::class, [
                "label" => "Votre avatar *",
                "attr" => [
                    "placeholder" => "Votre avatar"
                ],
                "help" => "* L'url d'un avatar"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
