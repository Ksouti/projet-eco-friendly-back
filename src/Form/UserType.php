<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("firstname", TextType::class, [
                "label" => "Prénom",
                "attr" => [
                    "placeholder" => "Prénom"
                ]
            ])

            ->add("lastname", TextType::class, [
                "label" => "Nom",
                "attr" => [
                    "placeholder" => "Nom"
                ]
            ])

            ->add("nickname", TextType::class, [
                "label" => 'Pseudo *',
                "attr" => [
                    "placeholder" => "Pseudo"
                ],
                'help' => '* obligatoire',
            ])

            ->add('email', EmailType::class, [
                "label" => "Email *",
                "attr" => [
                    "placeholder" => "Email"
                ],
                'help' => '* obligatoire',
            ])

            ->add('avatarFile', FileType::class, [
                "label" => "Avatar",
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
        ]);
    }
}
