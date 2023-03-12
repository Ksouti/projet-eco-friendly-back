<?php

namespace App\Security\Voter;

use App\Entity\Advice;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AdviceVoter extends Voter
{
    const ADVICE_READ       = 'advice_read';
    const ADVICE_EDIT       = 'advice_edit';
    const ADVICE_DELETE     = 'advice_delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ADVICE_READ, self::ADVICE_EDIT, self::ADVICE_DELETE])
            && $subject instanceof \App\Entity\Advice;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // you know $subject is a Advice object, thanks to `supports()`
        /** @var Advice $advice */
        $advice = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ADVICE_READ:
                // return true or false
                return $this->canEdit($advice, $user);
                break;
            case self::ADVICE_EDIT:
                // return true or false
                return $this->canEdit($advice, $user);
                break;
            case self::ADVICE_DELETE:
                // return true or false
                return $this->canDelete($advice, $user);
                break;
        }

        return false;
    }

    /**
     * @param Advice $advice the subject of the voter
     * @param User $user the user requesting action on the subject
     * @return bool true if current user match advice user
     */
    private function canRead(Advice $advice, User $user)
    {
        // return true or false
        return $user === $advice->getContributor() || ($advice->getStatus() !== 0 && $this->security->isGranted('ROLE_ADMIN'));
    }
    private function canEdit(Advice $advice, User $user)
    {
        // return true or false
        return ($user === $advice->getContributor() && $advice->getStatus() !== 2) || $this->security->isGranted('ROLE_ADMIN');
    }

    private function canDelete(Advice $advice, User $user)
    {
        // return true or false
        return ($user === $advice->getContributor() && $advice->getStatus() !== 2);
    }
}
