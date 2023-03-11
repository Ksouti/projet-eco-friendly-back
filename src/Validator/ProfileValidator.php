<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ProfileValidator extends ConstraintValidator
{
    /**
     * @param User $user
     */
    public function validate($user, Constraint $constraint): void
    {
        if (!$user instanceof User) {
            throw new UnexpectedValueException($user, User::class);
        }

        if (!$constraint instanceof Profile) {
            throw new UnexpectedValueException($constraint, Profile::class);
        }

        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $nickname = $user->getNickname();
        // Because they are not mapped, the new password and its confirmation 
        // are extracted from the form manually
        $passwordNew = $this->context->getRoot()->get('new_password')->getData();
        $passwordConfirm = $this->context->getRoot()->get('confirm_password')->getData();

        if (empty($firstname)) {
            $this->context->buildViolation($constraint->firstnameIsEmpty)
                ->atPath('firstname')
                ->addViolation();
        }

        if (empty($lastname)) {
            $this->context->buildViolation($constraint->lastnameIsEmpty)
                ->atPath('lastname')
                ->addViolation();
        }

        if (empty($nickname)) {
            $this->context->buildViolation($constraint->nicknameIsEmpty)
                ->atPath('nickname')
                ->addViolation();
        }

        if (empty($passwordNew)) {
            $this->context->buildViolation($constraint->passwordIsEmpty)
                ->atPath('new_password')
                ->addViolation();
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).{8,32}$/', $passwordNew)) {
            $this->context->buildViolation($constraint->passwordNotCompliant)
                ->atPath('new_password')
                ->addViolation();
        }

        if ($passwordNew !== $passwordConfirm) {
            $this->context->buildViolation($constraint->passwordsNoMatch)
                ->atPath('new_password')
                ->addViolation();
        }
    }
}
