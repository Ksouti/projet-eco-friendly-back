<?php

namespace App\Security\Voter;

use \App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const ARTICLE_SHOW = 'article_show';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ARTICLE_SHOW])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
    
        // you know $subject is a Post object, thanks to `supports()`
        /** @var Article $article */
        $article = $subject;


        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ARTICLE_SHOW:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canShow($article, $user);
                break;
        }

        return false;
    }

     /**
     * @param Article $article the subject of the voter
     * @param User $user the current user 
     * @return bool true if current user match advice user
     */
    private function canShow(Article $article, User $user){ 

        // renvoi true ou false
        return $user === $article->getAuthor();
    }
}
