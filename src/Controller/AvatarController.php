<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\AvatarType;
use App\Repository\AvatarRepository;
use App\Service\SluggerService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvatarController extends AbstractController
{
    /**
     * @Route("back_office/avatars", name="app_backoffice_avatars_list", methods={"GET"})
     */
    public function list(AvatarRepository $avatarRepository): Response
    {
        return $this->render('avatar/list.html.twig', [
            'avatars' => $avatarRepository->findAll(),
        ]);
    }

    /**
     * @Route("back_office/avatars/ajouter", name="app_backoffice_avatars_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SluggerService $slugger, AvatarRepository $avatarRepository): Response
    {
        $avatar = new Avatar();
        $avatar->setCreatedAt(new DateTimeImmutable());
        $form = $this->createForm(AvatarType::class, $avatar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $picture = $form->get('picture')->getData();
            if ($picture) {
                $pictureName = $slugger->slugify($avatar->getName()) . '.' . $picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('assets_avatar_directory'),
                        $pictureName
                    );
                    $avatar->setPicture($this->getParameter('assets_avatar_url') . $pictureName);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $avatarRepository->add($avatar, true);

            return $this->redirectToRoute('app_backoffice_avatars_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avatar/new.html.twig', [
            'avatar' => $avatar,
            'form' => $form,
        ]);
    }

    /**
     * @Route("back_office/avatars/{id}", name="app_backoffice_avatars_show", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function show(Avatar $avatar): Response
    {
        return $this->render('avatar/show.html.twig', [
            'avatar' => $avatar,
        ]);
    }

    /**
     * @Route("back_office/avatars/{id}/modifier", name="app_backoffice_avatars_edit", requirements={"id":"\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, SluggerService $slugger, Avatar $avatar, AvatarRepository $avatarRepository): Response
    {
        $form = $this->createForm(AvatarType::class, $avatar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $avatar->setUpdatedAt(new DateTimeImmutable());

            $picture = $form->get('picture')->getData();
            if ($picture) {
                $pictureName = $slugger->slugify($avatar->getName()) . '.' . $picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('assets_avatar_directory'),
                        $pictureName
                    );
                    $avatar->setPicture($this->getParameter('assets_avatar_url') . $pictureName);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $avatarRepository->add($avatar, true);

            return $this->redirectToRoute('app_backoffice_avatars_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avatar/edit.html.twig', [
            'avatar' => $avatar,
            'form' => $form,
        ]);
    }

    /**
     * @Route("back_office/avatars/{id}/desactiver", name="app_backoffice_avatars_deactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function deactivate(Request $request, Avatar $avatar, AvatarRepository $avatarRepository): Response
    {
        if ($this->isCsrfTokenValid('deactivate' . $avatar->getId(), $request->request->get('_token'))) {
            $avatar->$avatarRepository->add($avatar, true);
        }

        return $this->redirectToRoute('app_backoffice_avatars_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("back_office/avatars/{id}/reactiver", name="app_backoffice_avatars_reactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function reactivate(Request $request, Avatar $avatar, AvatarRepository $avatarRepository): Response
    {
        if ($this->isCsrfTokenValid('reactivate' . $avatar->getId(), $request->request->get('_token'))) {
            $avatarRepository->add($avatar, true);
        }

        return $this->redirectToRoute('app_backoffice_avatars_list', [], Response::HTTP_SEE_OTHER);
    }
}
