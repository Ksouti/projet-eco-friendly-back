<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("firstname", TextType::class, [
            "label" => "Prénom",
            "attr" => [
                "placeholder" => "Ada"
            ]
        ])

            ->add("lastname", TextType::class, [
                "label" => "Nom",
                "attr" => [
                    "placeholder" => "Lovelace"
                ]
            ])

            ->add("nickname", TextType::class, [
                "label" => 'Pseudo',
                "attr" => [
                    "placeholder" => "AdaLovelace1815"
                ]
            ])

            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "placeholder" => "ada.lovelace@gmail.com"
                ]
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'nouveau-mot-de-passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au mois {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('avatar', UrlType::class, [
                "label" => "Votre avatar *",
                "attr" => [
                    "placeholder" => "Votre avatar"
                ],
                "help" => "* L'url d'un avatar"
            ]);

        /* 
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]) */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
