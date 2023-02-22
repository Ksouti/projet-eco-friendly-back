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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/api/articles", name="app_api_articles_list")
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function list(Request $request, ArticleRepository $articleRepository): Response
    {
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
        return $this->json($articleRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'articles']);
=======
        return $this->json(
            $articleRepository->findAll(),
=======
        $category = $request->get('category', null);
        $status = $request->get('status', null);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', ($page - 1) * $limit ?? 0);
        $sortType = $request->get('sorttype', 'created_at');
        $order = $request->get('order', 'desc');
        $search = $request->get('search', null);

        return $this->json(
<<<<<<< HEAD
            $articleRepository->findAllWithParameters($category, $status, $page, $offset, $limit, $sortType, $order, $search),
>>>>>>> WIP: api Articles list
=======
            $articleRepository->findAllWithParameters($category, $status, $limit, $offset, $sortType, $order, $search),
>>>>>>> WIP: Api Articles list with parameters
            Response::HTTP_OK,
            [],
            ['groups' => 'articles']
        );
<<<<<<< HEAD
>>>>>>> FEAT: ArticleController added with groups on relevant entities
=======
        return $this->json($articleRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'articles']);
>>>>>>> FEAT: AdviceController (not list)  +  ArticleController (not list) + UserController read
=======
=======
    public function index(ParamFetcher $paramFetcher, ArticleRepository $articleRepository): Response
    {
>>>>>>> WIP: api lists param
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        return $this
            ->json($articleRepository
                ->findAllWithParameters($offset, $limit), Response::HTTP_OK, [], ['groups' => 'articles']);
<<<<<<< HEAD
>>>>>>> WIP: api lists param
=======
        return $this->json($articleRepository->findAllWithParameters($request->query->all()), Response::HTTP_OK, [], ['groups' => 'articles']);
>>>>>>> FEAT: Api ArticleController working
=======
>>>>>>> WIP: api Articles list
=======
>>>>>>> WIP: api lists param
=======
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        return $this->json($articleRepository->findAllWithParameters($request->query->all()), Response::HTTP_OK, [], ['groups' => 'articles']);
>>>>>>> FEAT: Api ArticleController working
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
