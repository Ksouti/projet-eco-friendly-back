<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
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
        return $this->redirectToRoute('app_backoffice_articles_list');
    }
}
