<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Entity\Article;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_root", methods={"GET"})
     * @Route("/back_office", name="app_backoffice_root", methods={"GET"})
     */
    public function root(): Response
    {
        return $this->redirectToRoute('app_backoffice_home');
    }

    /**
     * @Route("/back_office/home", name="app_backoffice_home", methods={"GET"})
     */
    public function home(EntityManagerInterface $entityManager): Response
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->render('home/admin.html.twig', [
                'user' => $this->getUser(),
                'members' => $entityManager->getRepository(User::class)->findMembersForHome(),
                'authors' => $entityManager->getRepository(User::class)->findAuthorsForHome(),
                'articles' => $entityManager->getRepository(Article::class)->findForHome(),
                'advices' => $entityManager->getRepository(Advice::class)->findForHome(),
            ]);
        }
        return $this->render('home/author.html.twig', [
            'user' => $this->getUser(),
            'articles' => $entityManager->getRepository(Article::class)->findAllByUser($this->getUser()),
        ]);
    }
}
