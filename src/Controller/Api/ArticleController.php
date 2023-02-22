<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    // TODO : add url parameters to filter articles
    /**
     * @Route("/api/articles", name="app_api_articles_list")
     */
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        return $this->json($articleRepository->findAllWithParameters($request->query->all()), Response::HTTP_OK, [], ['groups' => 'articles']);
    }

    /**
     * @Route("/api/articles/{id}", name="app_api_articles_read", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function read(?Article $article, ArticleRepository $articleRepository): Response
    {
        if (!$article) {
            return $this->json(['errors' => 'Cet article n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($articleRepository->find($article->getId()), Response::HTTP_OK, [], ['groups' => 'articles']);
    }
}
