<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Form\AdviceType;
use App\Repository\AdviceRepository;
use App\Service\SluggerService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdviceController extends AbstractController
{
    /**
     * @Route("/back_office/conseils", name="app_backoffice_advices_list", methods={"GET"})
     */
    public function list(AdviceRepository $adviceRepository): Response
    {
        return $this->render('advice/list.html.twig', [
            'advices' => $adviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/back_office/conseils/{id}", name="app_backoffice_advices_show", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function show(Advice $advice): Response
    {
        return $this->render('advice/show.html.twig', [
            'advice' => $advice,
        ]);
    }

    /**
     * @Route("/back_office/conseils/{id}/editer", name="app_backoffice_advices_edit", requirements={"id":"\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, SluggerService $slugger, Advice $advice, AdviceRepository $adviceRepository): Response
    {
        $form = $this->createForm(AdviceType::class, $advice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $advice->setSlug($slugger->slugify($advice->getTitle()));
            $advice->setUpdatedAt(new DateTimeImmutable());
            $adviceRepository->add($advice, true);

            return $this->redirectToRoute('app_backoffice_advices_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('advice/edit.html.twig', [
            'advice' => $advice,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/conseils/{id}/desactiver", name="app_backoffice_advices_deactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function deactivate(Advice $advice, AdviceRepository $adviceRepository): Response
    {
        $advice->setStatus(2);
        $adviceRepository->add($advice, true);
        return $this->redirectToRoute('app_backoffice_advices_list', [], Response::HTTP_SEE_OTHER);
    }
}
