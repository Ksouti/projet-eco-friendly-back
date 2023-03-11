<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("firstname", TextType::class, [
                "required" => true,
                "label" => "Prénom",
                "attr" => [
                    "placeholder" => "Entrez votre prénom"
                ]
            ])

            ->add("lastname", TextType::class, [
                "required" => true,
                "label" => "Nom",
                "attr" => [
                    "placeholder" => "Entrez votre nom de famille"
                ]
            ])

            ->add("nickname", TextType::class, [
                "required" => true,
                "label" => 'Pseudo',
                "attr" => [
                    "placeholder" => "Entrez votre pseudo"
                ]
            ])

            ->add('new_password', PasswordType::class, [
                "mapped" => false,
                "label" => "Nouveau mot de passe",
                "attr" => [
                    "placeholder" => "Mot de passe"
                ]
            ])

            ->add('confirm_password', PasswordType::class, [
                "label" => "Confirmation du mot de passe",
                "mapped" => false,
                "attr" => [
                    "placeholder" => "Confirmation du mot de passe"
                ]
            ])

            ->add('email', EmailType::class, [
                "required" => true,
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Email"
                ]
            ])

            ->add('avatarFile', FileType::class, [
                "label" => "Modifier mon avatar",
                "mapped" => false,
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => new Profile()
        ]);
    }
}
