<?php

namespace App\EventListener;

use App\Entity\Advice;
use App\Entity\Article;
use App\Entity\Category;
use App\Service\SluggerService;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class TitleListener
{
    private $slugger;

    public function __construct(SluggerService $slugger)
    {
        $this->slugger = $slugger;
    }

    // This method will be called when an Article entity is created or updated.
    // It will set a slug using the title property.
    public function updateArticleSlug(Article $article, LifecycleEventArgs $event): void
    {
        if (null === $article->getTitle()) {
            return;
        }

        $article->setSlug($this->slugger->slugify($article->getTitle()));
    }

    // This method will be called when an Advice entity is created or updated.
    // It will set a slug using the title property.
    public function updateAdviceSlug(Advice $advice, LifecycleEventArgs $event): void
    {
        if (null === $advice->getTitle()) {
            return;
        }

        $advice->setSlug($this->slugger->slugify($advice->getTitle()));
    }

    // This method will be called when a Category entity is created or updated.
    // It will set a slug using the name property.
    public function updateCategorySlug(Category $category, LifecycleEventArgs $event): void
    {
        if (null === $category->getName()) {
            return;
        }

        $category->setSlug($this->slugger->slugify($category->getName()));
    }
}
