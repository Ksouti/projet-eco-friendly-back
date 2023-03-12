<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const USER_READ   = 'user_read';
    const USER_UPDATE = 'user_update';
    const USER_DELETE = 'user_delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::USER_READ, self::USER_UPDATE, self::USER_DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // you know $subject is a User object, thanks to `supports()`
        /** @var User $user */
        $userSubject = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::USER_READ:
                // logic to determine if the user can read
                // return true or false
                return $this->canRead($userSubject, $user);
                break;
            case self::USER_UPDATE:
                // logic to determine if the user can update
                // return true or false
                return $this->canUpdate($userSubject, $user);
                break;
            case self::USER_DELETE:
                // logic to determine if the user can delete
                // return true or false
                return $this->canDelete($userSubject, $user);
                break;
        }

        return false;
    }

    /**
     * @param User $userSubject the subject of the voter
     * @param User $user the user requesting action on the subject
     * @return bool 
     */
    private function canRead(User $userSubject, User $user)
    {
        // return true or false
        return $userSubject === $user || $this->security->isGranted('ROLE_ADMIN');
    }

    private function canUpdate(User $userSubject, User $user)
    {
        // return true or false
        return $userSubject === $user || ($this->security->isGranted('ROLE_ADMIN') && $userSubject->getRoles() !== ['ROLE_USER']);
    }

    private function canDelete(User $userSubject, User $user)
    {
        // return true or false
        return $userSubject === $user;
    }
}
