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

            $this->addFlash(
                'success',
                'La catégorie "' . $category->getName() . '" a bien été ajoutée'
            );

            return $this->redirectToRoute('app_backoffice_categories_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/categories/{id}", name="app_backoffice_categories_show", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/back_office/categories/{id}/editer", name="app_backoffice_categories_edit", requirements={"id":"\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, SluggerService $slugger, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setSlug($slugger->slugify($category->getName()));
            $category->setUpdatedAt(new DateTimeImmutable());
            $categoryRepository->add($category, true);
            $this->addFlash(
                'success',
                'La catégorie "' . $category->getName() . '" a bien été modifiée'
            );

            return $this->redirectToRoute('app_backoffice_categories_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/categories/{id}/desactiver", name="app_backoffice_categories_deactivate", requirements={"id":"\d+"}, methods={"POST"})
    
     */
    public function deactivate(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('deactivate' . $category->getId(), $request->request->get('_token'))) {
            $category->setIsActive(false);
            $category->setUpdatedAt(new DateTimeImmutable());
            $categoryRepository->add($category, true);
        }

        $this->addFlash(
            'danger',
            'La catégorie "' . $category->getName() . '" a bien été désactivée.'
        );
        return $this->redirectToRoute('app_backoffice_categories_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/back_office/categories/{id}/reactiver", name="app_backoffice_categories_reactivate", requirements={"id":"\d+"}, methods={"POST"})
     
     */
    public function reactivate(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('reactivate' . $category->getId(), $request->request->get('_token'))) {
            $category->setIsActive(true);
            $category->setUpdatedAt(new DateTimeImmutable());
            $categoryRepository->add($category, true);
        }
        $this->addFlash(
            'success',
            'La catégorie "' . $category->getName() . '" a bien été réactivée.'
        );

        return $this->redirectToRoute('app_backoffice_categories_list', [], Response::HTTP_SEE_OTHER);
    }
}
