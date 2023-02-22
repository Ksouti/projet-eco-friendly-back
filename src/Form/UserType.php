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
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "Auteur" => "ROLE_AUTHOR",
                    "Admin" =>  "ROLE_ADMIN",
                    "User" =>   "ROLE_USER"
>>>>>>> FIX: access_control
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
                "help" => "* L'url d'un avatar"
            ])
            /*
            ->add('is_active' ,ChoiceType::class,[
                "choices" => [
                    "Activé" => "0",
                    "Désactivé" => "1"
                ],
                "label" => "Activé ou désactivé"
            ])
<<<<<<< HEAD
     */      
>>>>>>> FIX: debug UserType
        ;
=======
     */;
>>>>>>> FIX: access_control
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
