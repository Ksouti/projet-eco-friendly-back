<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\SluggerService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Role\Role;

class ArticleController extends AbstractController
{
    /**
     * @Route("/back_office/articles", name="app_backoffice_articles_list", methods={"GET"})
     */
    public function list(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/list.html.twig', [
            'articles' => $articleRepository->findAllOrderByDate(),
        ]);
    }

    /**
     * @Route("/back_office/auteurs/{id}", name="app_backoffice_articles_user", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function findAllByUser(User $author, ArticleRepository $articleRepository): Response
    {
        // Vérifier si l'utilisateur connecté est bien l'auteur des articles
        if ($this->getUser() !== $author && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }
    
        return $this->render('article/list.html.twig', [
            'articles' => $articleRepository->findAllByUser($author),
        ]);
    }

    /**
     * @Route("/back_office/articles/ajouter", name="app_backoffice_articles_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SluggerService $slugger, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
        $article->setCreatedAt(new DateTimeImmutable());
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugger->slugify($article->getTitle()));
            $articleRepository->add($article, true);

            return $this->redirectToRoute('app_backoffice_articles_show', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/articles/{id}", name="app_backoffice_articles_show", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/back_office/articles/{id}/editer", name="app_backoffice_articles_edit", requirements={"id":"\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, SluggerService $slugger, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugger->slugify($article->getTitle()));
            $article->setUpdatedAt(new DateTimeImmutable());
            $articleRepository->add($article, true);

            return $this->redirectToRoute('app_backoffice_articles_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/articles/{id}/desactiver", name="app_backoffice_articles_deactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function deactivate(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('deactivate' . $article->getId(), $request->request->get('_token'))) {
            $article->setStatus(2);
            $articleRepository->add($article, true);
        }
        return $this->redirectToRoute('app_backoffice_articles_list', [], Response::HTTP_SEE_OTHER);
    }

     /**
     * @Route("/back_office/articles/{id}/reactiver", name="app_backoffice_articles_reactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function reactivate(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('reactivate' . $article->getId(), $request->request->get('_token'))) {
            $article->setStatus(1);
            $articleRepository->add($article, true);
        }
        return $this->redirectToRoute('app_backoffice_articles_list', [], Response::HTTP_SEE_OTHER);
    }
}
