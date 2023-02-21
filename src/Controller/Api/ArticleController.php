<?php

namespace App\Controller\Api;

use App\Entity\Article;
<<<<<<< HEAD
<<<<<<< HEAD
use App\Entity\User;
=======
use App\Entity\Category;
>>>>>>> FEAT: ArticleController added with groups on relevant entities
=======
>>>>>>> FEAT: AdviceController (not list)  +  ArticleController (not list) + UserController read
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    // TODO : add url parameters to filter articles
    /**
     * @Route("/api/articles", name="app_api_articles_list")
     */
    public function index(ArticleRepository $articleRepository): Response
    {
<<<<<<< HEAD
<<<<<<< HEAD
        return $this->json($articleRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'articles']);
=======
        return $this->json(
            $articleRepository->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'articles']
        );
>>>>>>> FEAT: ArticleController added with groups on relevant entities
=======
        return $this->json($articleRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'articles']);
>>>>>>> FEAT: AdviceController (not list)  +  ArticleController (not list) + UserController read
    }

    /**
     * @Route("/api/articles/{id}", name="app_api_articles_read", requirements={"id":"\d+"}, methods={"GET"})
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function read(?Article $article, ArticleRepository $articleRepository): Response
    {
        if (!$article) {
            return $this->json(['errors' => 'Cet article n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($articleRepository->find($article->getId()), Response::HTTP_OK, [], ['groups' => 'articles']);
    }

     /**
     * @Route("/api/authors/{id}", name="app_backoffice_articles_findAllByUser", requirements={"id":"\d+"}, methods={"GET", "PUT", "DELETE"})
     */
    public function findAllByUser(User $author, ArticleRepository $articleRepository): Response
    {
        return $this->json($articleRepository->findAllOrderByUserId($author), Response::HTTP_OK, [], ['groups' => 'articles']);
    }
=======
    public function read(Article $article, ArticleRepository $articleRepository): Response
=======
    public function read(?Article $article, ArticleRepository $articleRepository): Response
>>>>>>> FEAT: AdviceController (not list)  +  ArticleController (not list) + UserController read
    {
        if (!$article) {
            return $this->json(['errors' => 'Cet article n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($articleRepository->find($article->getId()), Response::HTTP_OK, [], ['groups' => 'articles']);
    }
>>>>>>> FEAT: ArticleController added with groups on relevant entities
}
