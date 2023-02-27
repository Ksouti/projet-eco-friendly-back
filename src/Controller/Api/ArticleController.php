<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ArticleController extends AbstractController
{
   
    /**
     * @Route("/api/articles", name="app_api_articles_list")
     */
    public function list(Request $request, ArticleRepository $articleRepository): Response
    {
        $category = $request->get('category', null);
        $status = $request->get('status', null);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', ($page - 1) * $limit ?? 0);
        $sortType = $request->get('sorttype', 'created_at');
        $order = $request->get('order', 'desc');
        $search = $request->get('search', null);

        return $this->json(
            $articleRepository->findAllWithParameters($category, $status, $limit, $offset, $sortType, $order, $search),
            Response::HTTP_OK,
            [],
            ['groups' => 'articles']
        );
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

   //  /**
   // * @Route("/api/authors/{id}", name="app_api_articles_findAllByUser", requirements={"id":"\d+"}, methods={"GET", "PUT", "DELETE"})
   // */
   // public function findAllByUser(User $author, ArticleRepository $articleRepository): Response
   // {
   //     return $this->json($articleRepository->findAllOrderByUserId($author), Response::HTTP_OK, [], ['groups' => 'articles']);
   // }

}
