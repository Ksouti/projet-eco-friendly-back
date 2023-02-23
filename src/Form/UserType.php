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
<<<<<<< HEAD
            ->add('email')
            ->add('roles',ChoiceType::class,[
                "choices"=>[
                    "Manager" => "ROLE_AUTHOR",
                    "Admin" => "ROLE_ADMIN",
                    "User" => "ROLE_USER"
=======
            ->add('email', EmailType::class, [
                "label" => "L'email",
                "attr" => [
                    "placeholder" => "L'email"
                ]
            ])
<<<<<<< HEAD
<<<<<<< HEAD
=======

>>>>>>> FIX: merge conflicts management
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "Auteur" => "ROLE_AUTHOR",
                    "Admin" =>  "ROLE_ADMIN",
<<<<<<< HEAD
                    "User" =>   "ROLE_USER"
<<<<<<< HEAD
>>>>>>> FIX: access_control
=======
                ->add('roles',ChoiceType::class,[
                "choices"=>[
                    "Author" => "ROLE_AUTHOR",
                    "Admin" => "ROLE_ADMIN",
                    "User" => "ROLE_USER"
>>>>>>> FIX: route correction and UserType correction
=======
>>>>>>> FIX: fixtures loading ok
=======
                    "Utilisateur" => "ROLE_USER"
>>>>>>> FIX: merge conflicts management
                ],
                "expanded" => true,
                "multiple" => true
            ])
<<<<<<< HEAD
<<<<<<< HEAD
            ->add('password')
            ->add('firstname')
            ->add('lastname')
            ->add('nickname')
            ->add('avatar')
            ->add('is_active')
            ->add('created_at')
            ->add('updated_at')
=======
           
=======

>>>>>>> FIX: access_control
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
<<<<<<< HEAD
<<<<<<< HEAD
                "help" => "* L'url d'un avatar"
            ])
<<<<<<< HEAD
            /*
=======
                "help"=> "* L'url d'un avatar"
            ]) 
/*
>>>>>>> FIX: route correction and UserType correction
            ->add('is_active' ,ChoiceType::class,[
                "choices" => [
                    "Activé" => "0",
                    "Désactivé" => "1"
                ],
                "label" => "Activé ou désactivé"
            ])
<<<<<<< HEAD
<<<<<<< HEAD
     */      
>>>>>>> FIX: debug UserType
=======
     */      
>>>>>>> FIX: route correction and UserType correction
        ;
=======
     */;
>>>>>>> FIX: access_control
=======
=======
>>>>>>> FIX: merge conflicts management

                "help"=> "* L'url d'un avatar"
            ]) 

            ->add('is_active' ,ChoiceType::class,[
                "choices" => [
                    "Activé" => "1",
                    "Desactivé" => "0"
                ],
<<<<<<< HEAD
                "label" => "Activé ou désactivé"
<<<<<<< HEAD
=======
                "label" => "Activer ou désactiver"
>>>>>>> Update UserType.php
            ]);
>>>>>>> FIX: fixtures loading ok
=======
            ])
>>>>>>> FIX: merge conflicts management
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
