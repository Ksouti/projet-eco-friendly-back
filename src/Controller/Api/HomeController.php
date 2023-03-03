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
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $articles = [];
        $advices = [];

        foreach ($categories as $category) {
            $articles[] = $entityManager->getRepository(Article::class)->findLatestByCategory(1, $category->getId())[0];
            $advices[] = $entityManager->getRepository(Advice::class)->findLatestByCategory(1, $category->getId())[0];
        }
        $homeContent = [
            'articles' => $articles,
            'advices' => $advices
        ];
        $jsonContent = $serializer->serialize($homeContent, 'json', ['groups' => ['articles', 'advices']]);
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
