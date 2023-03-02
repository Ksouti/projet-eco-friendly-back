<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\AdviceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
   /**
 * @Route("/api/users/{id}", name="app_api_users_read", requirements={"id":"\d+"}, methods={"GET"})
 */
public function read(?User $user, UserRepository $userRepository): Response
{
    // Vérifier si l'utilisateur existe
    if (!$user) {
        return $this->json(['errors' => 'Cet utilisateur n\'existe pas'], Response::HTTP_NOT_FOUND);
    }
    
    // Vérifier si l'utilisateur connecté est le propriétaire des données
    if ($this->getUser() !== $user) {
        throw new AccessDeniedException('Access Denied.');
    }

    return $this->json($userRepository->find($user->getId()), Response::HTTP_OK, [], ['groups' => 'users']);
}


    /**
     * @Route("/api/users/{id}", name="app_api_users_update", requirements={"id":"\d+"}, methods={"PUT"})
     */
    public function update(Request $request, ?User $user, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, UserRepository $userRepository): Response
    {
        if (!$user) {
            return $this->json(['errors' => ['Utilisateur' => 'Cet utilisateur n\'existe pas']], Response::HTTP_NOT_FOUND);
        }

     // Vérifier si l'utilisateur connecté est le propriétaire des données
        if ($this->getUser() !== $user) {
        throw new AccessDeniedException('Access Denied.');
    }
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
            $data = json_decode($request->getContent(), true);
            $user->setEmail($data['email'] ?? $user->getEmail());
            $user->setPassword($passwordHasher->hashPassword($user, $data['password'] ?? $user->getPassword()));
            $user->setFirstname($data['firstname'] ?? $user->getFirstname());
            $user->setLastname($data['lastname'] ?? $user->getLastname());
            $user->setNickname($data['nickname'] ?? $user->getNickname());
            $user->setAvatar($data['avatar'] ?? $user->getAvatar());
            $user->setUpdatedAt(new \DateTimeImmutable());
        } catch (NotEncodableValueException $e) {
            return $this->json(['errors' => 'Json non valide'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(
                ['errors' => $errorsArray],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $userRepository->add($user, true);

        return $this->json(
            $user,
            Response::HTTP_OK,
            [
                'Location' => $this->generateUrl('app_api_users_read', ['id' => $user->getId()]),
            ],
            [
                'groups' => 'users',
            ]
        );
    }

    /**
     * @Route("/api/users/{id}/avatar", name="app_api_users_avatar", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function avatarUpload(Request $request, ?User $user, UserRepository $userRepository): Response
    {
        if (!$user) {
            return $this->json(['errors' => ['Utilisateur' => 'Cet utilisateur n\'existe pas']], Response::HTTP_NOT_FOUND);
        }

        $avatar = $request->files->get('avatar');

        if (!$avatar) {
            return $this->json(['errors' => 'Image non valide'], Response::HTTP_BAD_REQUEST);
        }

        $filename = $user->getId() . '-' . uniqid() . '.' . $avatar->guessExtension();

        try {
            $avatar->move(
                $this->getParameter('uploads_user_directory'),
                $filename
            );
            $user->setAvatar($this->getParameter('uploads_user_url') . $filename);
        } catch (FileException $e) {
            return $this->json(['errors' => 'Une erreur est survenue lors de l\'upload de l\'image'], Response::HTTP_BAD_REQUEST);
        }

        $userRepository->add($user, true);

        return $this->json(
            $user,
            Response::HTTP_OK,
            ['Location' => $this->generateUrl('app_api_users_read', ['id' => $user->getId()])],
            ['groups' => 'users']
        );
    }

    /**
     * @Route("/api/users/{id}", name="app_api_users_delete", requirements={"id":"\d+"}, methods={"DELETE"})
     */
    public function delete(?User $user, UserRepository $userRepository, AdviceRepository $adviceRepository): Response
    {
        if (!$user) {
            return $this->json(['errors' => ['user' => 'Cet utilisateur n\'existe pas']], Response::HTTP_NOT_FOUND);
        }
        // Vérifier si l'utilisateur connecté est le propriétaire des données
        if ($this->getUser() !== $user) {
            throw new AccessDeniedException('Access Denied.');

        // reatribute articles and advices to admin
        // TODO : create a service to do this and an anonyminous user to dump articles and advices
        $advices = $user->getAdvices();
        foreach ($advices as $advice) {
            $advice->setContributor($userRepository->find(1));
            $adviceRepository->add($advice, true);
        }
        $userRepository->remove($user, true);
        return $this->json([], Response::HTTP_NO_CONTENT, [], ['groups' => 'users']);
    }
}
