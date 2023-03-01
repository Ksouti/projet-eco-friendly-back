<?php

namespace App\Controller\Api;

use App\Entity\Advice;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{

    /**
     * @Route("/api/home", name="app_api_home_list")
     */
    public function list(EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        // return a json with the last article and last advice for each category
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $homeContent = [];
        foreach ($categories as $category) {
            $homeContent[$category->getName()]['Article'] = json_decode($serializer
                ->serialize($entityManager->getRepository(Article::class)->findForHome(1, 1, $category->getId()), 'json', ['groups' => 'articles']), true, 512, JSON_UNESCAPED_SLASHES);
            $homeContent[$category->getName()]['Conseil'] = json_decode($serializer
                ->serialize($entityManager->getRepository(Advice::class)->findForHome(1, 1, $category->getId()), 'json', ['groups' => 'advices']), true, 512, JSON_UNESCAPED_SLASHES);
        }

        return $this->json($homeContent, Response::HTTP_OK);

        /* // return a json with the last article and last advice for each category
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $homeContent = [];
        foreach ($categories as $category) {
            $homeContent[$category->getName()]['Article'] = $serializer
                ->serialize($entityManager->getRepository(Article::class)->findForHome(1, 1, $category->getId()), 'json', ['groups' => 'articles']);
            $homeContent[$category->getName()]['Conseil'] = $serializer
                ->serialize($entityManager->getRepository(Advice::class)->findForHome(1, 1, $category->getId()), 'json', ['groups' => 'advices']);
        }

        return $this->json($homeContent, Response::HTTP_OK); */
    }
}
