<?php

namespace App\EventListener;

use App\Entity\Advice;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AdviceListener
{
    private $slugger;
    private $entityManager;

    public function __construct(SluggerService $slugger, EntityManagerInterface $EntityManager)
    {
        $this->slugger = $slugger;
        $this->entityManager = $EntityManager;
    }

    public function SetSlug(Advice $advice, LifecycleEventArgs $event): void
    {
        $advice->setSlug($this->slugger->slugify($advice->getTitle()));
        $this->entityManager->persist($advice);
        $this->entityManager->flush();
    }
}
