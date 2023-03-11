<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class Profile extends Constraint
{
    public string $firstnameIsEmpty = 'Veuillez renseigner votre prénom';

    public string $lastnameIsEmpty = 'Veuillez renseigner votre nom';

    public string $nicknameIsEmpty = 'Veuillez renseigner votre pseudo';

    public string $passwordIsEmpty = 'Veuillez renseigner un nouveau mot de passe';

    public string $passwordNotCompliant = 'Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial';

    public string $passwordsNoMatch = 'Les mots de passe ne correspondent pas';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
