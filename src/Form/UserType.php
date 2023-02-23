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
            ->add('email', EmailType::class, [
                "label" => "L'email",
                "attr" => [
                    "placeholder" => "L'email"
                ]
            ])
            ->add('roles', ChoiceType::class, [
                "choices" => [
<<<<<<< HEAD
                    "Auteur" => "ROLE_AUTHOR",
                    "Admin" =>  "ROLE_ADMIN",
                    "User" =>   "ROLE_USER"
=======
                    "Manager" => "ROLE_AUTHOR",
                    "Admin" => "ROLE_ADMIN"
>>>>>>> 023e8847b6a3c26c50db1eaed76e8d4474cdab1b
                ],
                "expanded" => true,
                "multiple" => true
            ])

            ->add("firstname", TextType::class, [
                "label" => "Prénom",
                "attr" => [
                    "placeholder" => "Entrez votre prénom"
                ]
            ])

            ->add("lastname", TextType::class, [
                "label" => "Nom de famille",
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

            ->add('avatar', UrlType::class, [
                "label" => "Votre avatar *",
                "attr" => [
                    "placeholder" => "Votre avatar"
                ],
                "help" => "* L'url d'un avatar"
            ])
<<<<<<< HEAD
            /*
            ->add('is_active' ,ChoiceType::class,[
                "choices" => [
                    "Activé" => "0",
                    "Désactivé" => "1"
                ],
                "label" => "Activé ou désactivé"
            ])
     */;
=======

            ->add('is_active', ChoiceType::class, [
                "choices" => [
                    "Activé" => "1",
                    "dasactivé" => "0"
                ],
                "label" => "Activé ou désactivé"
            ]);
>>>>>>> 023e8847b6a3c26c50db1eaed76e8d4474cdab1b
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
