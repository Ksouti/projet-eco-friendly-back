<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    /**
     * @Route("/back_office/home", name="app_backoffice_home", methods={"GET"})
     */
    public function home(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()->isActive()) {
            return $this->redirectToRoute('app_backoffice_security_login');
        } else if (!$this->getUser()->isVerified()) {
            return $this->redirectToRoute('app_backoffice_users_create', ['id' => $this->getUser()->getId()]);
        } else if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->render('home/admin.html.twig', [
                'user' => $this->getUser(),
                'members' => $entityManager->getRepository(User::class)->findMembersForHome(),
                'authors' => $entityManager->getRepository(User::class)->findAuthorsForHome(),
                'articles' => $entityManager->getRepository(Article::class)->findForHome(),
                'advices' => $entityManager->getRepository(Advice::class)->findForHome(),
            ]);
        } else {
            return $this->render('user/show.html.twig', [
                'user' => $this->getUser(),
                'articles' => $entityManager->getRepository(Article::class)->findAllByUser($this->getUser()),
            ]);
        }
    }
}
