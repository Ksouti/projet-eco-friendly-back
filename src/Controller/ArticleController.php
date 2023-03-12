<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\ContentListType;
use App\Repository\ArticleRepository;
use App\Service\SluggerService;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ArticleController extends AbstractController
{
    /**
     * @Route("/back_office/articles", name="app_backoffice_articles_list", methods={"GET", "POST"})
     * @isGranted("ROLE_ADMIN", message="Vous n'avez pas les droits pour accéder à cette page")
     */
    public function list(Request $request, ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllOrderByDate();

        $form = $this->createForm(ContentListType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articles = $articleRepository->findAllWithFilter(
                $form->get('title')->getData(),
                $form->get('content')->getData(),
                $form->get('status')->getData(),
                $form->get('user')->getData(),
                $form->get('category')->getData(),
                $form->get('sortType')->getData() ?? 'created_at',
                $form->get('sortOrder')->getData() ?? 'DESC',
                DateTimeImmutable::createFromMutable($form->get('dateFrom')->getData() ?? new DateTime('2000-01-01')),
                DateTimeImmutable::createFromMutable($form->get('dateTo')->getData() ?? new DateTime('now'))
            );

            return $this->render('article/list.html.twig', [
                'articles' => $articles,
                'form' => $form->createView()
            ]);
        }

        return $this->renderForm('article/list.html.twig', [
            'articles' => $articles,
            'form' => $form
        ]);
    }

    /**
     * @Route("/back_office/auteurs/{id}", name="app_backoffice_articles_user", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function findAllByUser(User $author, ArticleRepository $articleRepository): Response
    {
        // Checks if the user is the author or if the user is an admin
        if ($this->getUser() !== $author && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
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
        $article->setPicture('https://eco-friendly.fr/assets/img/misc/default-article-illustration.png');

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setSlug($slugger->slugify($article->getTitle()));

            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $pictureName = substr($slugger->slugify($article->getTitle()), 0, 10) . uniqid() . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('uploads_article_directory'),
                        $pictureName
                    );
                    $article->setPicture($this->getParameter('uploads_article_url') . $pictureName);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }
            // TODO: The following code works for downscaling the image but not for upscaling it
            // TODO: It has to be fixed, maybe to only square the image
            /*             if ($pictureFile) {

                $extension = $pictureFile->guessExtension();
                if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    $this->addFlash('danger', 'Format d\'image non supporté');
                    // return $this->redirectToRoute('app_backoffice_users_new');
                }

                $filename = substr($slugger->slugify($article->getTitle()), 0, 10) . uniqid() . '.' . $extension;
                $filepath = $this->getParameter('uploads_article_url') . '/' . $filename;

                try {
                    $pictureFile->move(
                        $this->getParameter('uploads_article_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }

                list($width, $height) = getimagesize($filepath);
                $size = min($width, $height); // get the minimum dimension
                $dst_x = ($width - $size) / 2;
                $dst_y = ($height - $size) / 2;
                $src_x = 0;
                $src_y = 0;
                $new_width = $new_height = 1000;

                if ($extension === 'png') {
                    $image = imagecreatefrompng($filepath);
                } else {
                    $image = imagecreatefromjpeg($filepath);
                }

                $new_image = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($new_image, $image, 0, 0, $src_x + $dst_x, $src_y + $dst_y, $new_width, $new_height, $size, $size);

                if ($extension === 'png') {
                    imagepng($new_image, $filepath);
                } else {
                    imagejpeg($new_image, $filepath);
                }

                imagedestroy($image);
                imagedestroy($new_image);

                $article->setPicture($this->getParameter('uploads_article_url') . $filename);
            } */

            $articleRepository->add($article, true);

            $this->addFlash(
                'success',
                '"' . $article->getTitle() . '" a bien été créé.'
            );

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_backoffice_articles_list', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('app_backoffice_articles_user', ['id' => $article->getAuthor()->getId()], Response::HTTP_SEE_OTHER);
            }
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
        $this->denyAccessUnlessGranted('article_read', $article);

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/back_office/articles/{id}/editer", name="app_backoffice_articles_edit", requirements={"id":"\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, SluggerService $slugger, Article $article, ArticleRepository $articleRepository): Response
    {
        $this->denyAccessUnlessGranted('article_edit', $article);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugger->slugify($article->getTitle()));
            $article->setUpdatedAt(new DateTimeImmutable());

            $pictureFile = $form->get('pictureFile')->getData();
            if ($pictureFile) {
                $pictureName = substr($slugger->slugify($article->getTitle()), 0, 10) . uniqid() . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('uploads_article_directory'),
                        $pictureName
                    );
                    $article->setPicture($this->getParameter('uploads_article_url') . $pictureName);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $articleRepository->add($article, true);


            $this->addFlash(
                'success',
                '"' . $article->getTitle() . '" a bien été modifié.'
            );

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_backoffice_articles_list', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('app_backoffice_articles_user', ['id' => $article->getAuthor()->getId()], Response::HTTP_SEE_OTHER);
            }
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
        $this->denyAccessUnlessGranted('article_deactivate', $article);

        if ($this->isCsrfTokenValid('deactivate' . $article->getId(), $request->request->get('_token'))) {
            $article->setStatus(2);
            $articleRepository->add($article, true);
        }

        $this->addFlash(
            'danger',
            '"' . $article->getTitle() . '" a été désactivé.'
        );
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_backoffice_articles_list', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_backoffice_articles_user', ['id' => $article->getAuthor()->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/back_office/articles/{id}/reactiver", name="app_backoffice_articles_reactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function reactivate(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $this->denyAccessUnlessGranted('article_reactivate', $article);

        if ($this->isCsrfTokenValid('reactivate' . $article->getId(), $request->request->get('_token'))) {
            $article->setStatus(1);
            $articleRepository->add($article, true);
        }
        $this->addFlash(
            'success',
            '"' . $article->getTitle() . '" a été réactivé.'
        );

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_backoffice_articles_list', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_backoffice_articles_user', ['id' => $article->getAuthor()->getId()], Response::HTTP_SEE_OTHER);
        }
    }
}
