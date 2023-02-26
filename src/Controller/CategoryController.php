<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\SluggerService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/back_office/categories", name="app_backoffice_categories_list", methods={"GET"})
     */
    public function list(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/list.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/back_office/categories/ajouter", name="app_backoffice_categories_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SluggerService $slugger, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt(new DateTimeImmutable());
            $category->setIsActive(true);
            $category->setSlug($slugger->slugify($category->getName()));
            $categoryRepository->add($category, true);

            return $this->redirectToRoute('app_backoffice_categories_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/categories/{id}", name="app_backoffice_categories_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/back_office/categories/{id}/editer", name="app_backoffice_categories_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, SluggerService $slugger, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setSlug($slugger->slugify($category->getName()));
            $categoryRepository->add($category, true);

            return $this->redirectToRoute('app_backoffice_categories_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/categories/{id}", name="app_backoffice_categories_deactivate", methods={"POST"})
     */
    public function deactivate(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('deactivate' . $category->getId(), $request->request->get('_token'))) {
            $category->setIsActive(false);
            $categoryRepository->add($category, true);
        }

        return $this->redirectToRoute('app_backoffice_categories_list', [], Response::HTTP_SEE_OTHER);
    }
}
