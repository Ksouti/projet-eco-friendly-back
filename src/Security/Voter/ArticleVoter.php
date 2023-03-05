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
                return $this->canEdit($article, $user);
                break;
            case self::ARTICLE_DEACTIVATE:
                // return true or false
                // J'appelle ma méthode canEdit pour vérifier si l'utilisateur a le droit
                return $this->canDeactivate($article, $user);
                break;
            case self::ARTICLE_REACTIVATE:
                // return true or false
                // J'appelle ma méthode canEdit pour vérifier si l'utilisateur a le droit
                return $this->canReactivate($article, $user);
                break;
        }

        return false;
    }

    /**
     * @param Article $article the subject of the voter
     * @param User $user the user requesting action on the subject
     * @return bool
     */
    private function canRead(Article $article, User $user)
    {
        return ($article->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }

    private function canEdit(Article $article, User $user)
    {
        return (($article->getAuthor() === $user && $article->getStatus() !== 2) || $this->security->isGranted('ROLE_ADMIN'));
    }

    private function canDeactivate(Article $article, User $user)
    {
        return ($article->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }

    private function canReactivate(Article $article, User $user)
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
