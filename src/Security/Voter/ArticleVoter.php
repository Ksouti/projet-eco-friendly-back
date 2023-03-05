<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    const ARTICLE_READ       = 'article_read';
    const ARTICLE_EDIT       = 'article_edit';
    const ARTICLE_DEACTIVATE = 'article_deactivate';
    const ARTICLE_REACTIVATE = 'article_reactivate';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array(
            $attribute,
            [
                self::ARTICLE_READ,
                self::ARTICLE_EDIT,
                self::ARTICLE_DEACTIVATE,
                self::ARTICLE_REACTIVATE
            ]
        )
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // you know $subject is a Article object, thanks to `supports()`
        /** @var Article $article */
        $article = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ARTICLE_READ:
                // return true or false
                // J'appelle ma méthode canEdit pour vérifier si l'utilisateur a le droit
                return $this->canRead($article, $user);
                break;
            case self::ARTICLE_EDIT:
                // return true or false
                // J'appelle ma méthode canEdit pour vérifier si l'utilisateur a le droit
                return $this->hasRight($article, $user);
                break;
            case self::ARTICLE_DEACTIVATE:
                // return true or false
                // J'appelle ma méthode canEdit pour vérifier si l'utilisateur a le droit
                return $this->hasRight($article, $user);
                break;
            case self::ARTICLE_REACTIVATE:
                // return true or false
                // J'appelle ma méthode canEdit pour vérifier si l'utilisateur a le droit
                return $this->hasRight($article, $user);
                break;
        }

        return false;
    }

    /**
     * @param Article $article the subject of the voter
     * @param User $user the current user 
     */
    private function hasRight(Article $article, User $user)
    {

        // return true or false
        return ($user === $article->getAuthor() && ($article->getStatus() !== 2));
    }

    /**
     * @param Article $article the subject of the voter
     * @param User $user the current user
     */
    private function canRead(Article $article, User $user)
    {
        return ($article->getAuthor() === $user || in_array('ROLE_ADMIN', $user->getRoles()));
    }

    private function canDeactivate(Article $article, User $user)
    {
        return ($article->getAuthor() === $user || in_array('ROLE_ADMIN', $user->getRoles()));
    }
}
