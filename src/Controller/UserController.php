<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CodeGeneratorService;
use App\Service\SluggerService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{
    /**
     * @Route("/back_office/utilisateurs/membres", name="app_backoffice_members_list", requirements={"id":"\d+"}, methods={"GET"})
     * @isGranted("ROLE_ADMIN", message="Accès réservé aux administrateurs")
     */
    public function listMembers(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->listAllMembers(),
        ]);
    }

    /**
     * @Route("/back_office/utilisateurs/auteurs", name="app_backoffice_authors_list", requirements={"id":"\d+"}, methods={"GET"})
     * @isGranted("ROLE_ADMIN", message="Accès réservé aux administrateurs")
     */
    public function listAuthors(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->listAllAuthors(),
        ]);
    }

    /**
     * @Route("/back_office/utilisateurs/ajouter", name="app_backoffice_users_new", methods={"GET" , "POST"})
     */
    public function new(Request $request, CodeGeneratorService $codeGeneratorService, SluggerService $slugger, UserRepository $userRepository): Response
    {
        $user = new User();
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setIsActive(true);
        $user->setCode($codeGeneratorService->codeGen());
        // ! TO REMOVE !
        $user->setPassword('testtest');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('avatar')->getData();
            if ($picture) {
                $pictureName = substr($slugger->slugify($user->getNickname()), 0, 10) . uniqid() . '.' . $picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('uploads_user_directory'),
                        $pictureName
                    );
                    $user->setAvatar($this->getParameter('uploads_user_url') . $pictureName);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_backoffice_members_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/back_office/utilisateurs/{id}", name="app_backoffice_users_show", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function show(User $user): Response
    {


        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/back_office/utilisateurs/{id}/modifier", name="app_backoffice_users_edit", requirements={"id":"\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, SluggerService $slugger, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('avatar')->getData();
            if ($picture) {
                $pictureName = substr($slugger->slugify($user->getNickname()), 0, 10) . uniqid() . '.' . $picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('uploads_user_directory'),
                        $pictureName
                    );
                    $user->setAvatar($this->getParameter('uploads_user_url') . $pictureName);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_backoffice_members_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/utilisateurs/{id}/desactiver", name="app_backoffice_users_deactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function deactivate(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('deactivate' . $user->getId(), $request->request->get('_token'))) {
            $user->setIsActive(false);
            $userRepository->add($user, true);
        }

        return $this->redirectToRoute('app_backoffice_members_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/back_office/utilisateurs/{id}/reactiver", name="app_backoffice_users_reactivate", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function reactivate(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('reactivate' . $user->getId(), $request->request->get('_token'))) {
            $user->setIsActive(true);
            $userRepository->add($user, true);
        }

        return $this->redirectToRoute('app_backoffice_members_list', [], Response::HTTP_SEE_OTHER);
    }
}
