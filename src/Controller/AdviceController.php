<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Form\AdviceType;
use App\Repository\AdviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/advice")
 */
class AdviceController extends AbstractController
{
    /**
     * @Route("/", name="app_advice_list", methods={"GET"})
     */
    public function index(AdviceRepository $adviceRepository): Response
    {
        return $this->render('advice/index.html.twig', [
            'advice' => $adviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_advice_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AdviceRepository $adviceRepository): Response
    {
        $advice = new Advice();
        $form = $this->createForm(AdviceType::class, $advice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adviceRepository->add($advice, true);

            return $this->redirectToRoute('app_advice_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('advice/new.html.twig', [
            'advice' => $advice,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_advice_show", methods={"GET"})
     */
    public function show(Advice $advice): Response
    {
        return $this->render('advice/show.html.twig', [
            'advice' => $advice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_advice_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Advice $advice, AdviceRepository $adviceRepository): Response
    {
        $form = $this->createForm(AdviceType::class, $advice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adviceRepository->add($advice, true);

            return $this->redirectToRoute('app_advice_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('advice/edit.html.twig', [
            'advice' => $advice,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_advice_delete", methods={"POST"})
     */
    public function delete(Request $request, Advice $advice, AdviceRepository $adviceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $advice->getId(), $request->request->get('_token'))) {
            $adviceRepository->remove($advice, true);
        }

        return $this->redirectToRoute('app_advice_list', [], Response::HTTP_SEE_OTHER);
    }
}
