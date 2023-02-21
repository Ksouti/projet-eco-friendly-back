<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users/{id}", name="app_api_users_read", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function read(?User $user, UserRepository $userRepository): Response
    {
        if (!$user) {
            return $this->json(['errors' => 'Cet utilisateur n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($userRepository->find($user->getId()), Response::HTTP_OK, [], ['groups' => 'users']);
    }
}
