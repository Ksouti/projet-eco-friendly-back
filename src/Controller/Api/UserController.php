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
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
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
            return $this->json(['errors' => ['user' => ['Cet utilisateur n\'existe pas']]], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si l'utilisateur connecté est le propriétaire des données
        $this->denyAccessUnlessGranted('user_read', $user);

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

        // Verify if the user is the owner of the data
        $this->denyAccessUnlessGranted('user_update', $user);

        // Clone the user to keep the original data for sensitive fields
        $originalUser = clone $user;

        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate' => $user]);
            $user->setFirstName(ucfirst($user->getFirstName()));
            $user->setLastName(ucfirst($user->getLastName()));
            // Keep the original data for sensitive fields
            $user->setPassword($originalUser->getPassword());
            $user->setCode($originalUser->getCode());
            $user->setRoles($originalUser->getRoles());
            $user->setEmail($originalUser->getEmail());
            $user->setPassword($originalUser->getPassword());
            $user->setIsActive($originalUser->isActive());
            $user->setIsVerified($originalUser->isVerified());
            $user->setCreatedAt($originalUser->getCreatedAt());
            $user->setUpdatedAt(new \DateTimeImmutable());
        } catch (NotEncodableValueException $e) {
            return $this->json(['errors' => ['json' => ['Json non valide']]], Response::HTTP_BAD_REQUEST);
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
    // TODO: Create a service to handle file upload & reuse it in several controllers
    public function avatarUpload(Request $request, ?User $user, UserRepository $userRepository): Response
    {
        if (!$user) {
            return $this->json(['errors' => ['Utilisateur' => 'Cet utilisateur n\'existe pas']], Response::HTTP_NOT_FOUND);
        }

        // Verify if the user is the owner of the data
        $this->denyAccessUnlessGranted('user_update', $user);

        $avatar = $request->files->get('avatar');

        if (!$avatar) {
            return $this->json(['errors' => ['picture' => ['Image non valide']]], Response::HTTP_BAD_REQUEST);
        }

        $extension = $avatar->guessExtension();
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return $this->json(['errors' => ['picture' => ['Format d\'image non supporté']]], Response::HTTP_BAD_REQUEST);
        }

        $filename = $user->getId() . '-' . uniqid() . '.' . $extension;
        $filepath = $this->getParameter('uploads_user_directory') . '/' . $filename;

        try {
            $avatar->move(
                $this->getParameter('uploads_user_directory'),
                $filename
            );
        } catch (FileException $e) {
            return $this->json(['errors' => ['picture' => ['Une erreur est survenue lors de l\'upload de l\'image']]], Response::HTTP_BAD_REQUEST);
        }

        list($width, $height) = getimagesize($filepath);
        $size = min($width, $height); // get the minimum dimension
        $dst_x = ($width - $size) / 2;
        $dst_y = ($height - $size) / 2;
        $src_x = 0;
        $src_y = 0;
        $new_width = $new_height = 80;

        if ($extension === 'png') {
            $image = imagecreatefrompng($filepath);
        } else {
            $image = imagecreatefromjpeg($filepath);
        }

        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $image, 0, 0, $src_x + $dst_x, $src_y + $dst_y, $new_width, $new_height, $size, $size);

        if ($extension === 'png') {
            imagepng($new_image, $filepath);
        } else {
            imagejpeg($new_image, $filepath);
        }

        imagedestroy($image);
        imagedestroy($new_image);

        $user->setAvatar($this->getParameter('uploads_user_url') . $filename);
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
            return $this->json(['errors' => ['user' => ['Cet utilisateur n\'existe pas']]], Response::HTTP_NOT_FOUND);
        }

        // Verify if the user is the owner of the data
        $this->denyAccessUnlessGranted('user_delete', $user);

        // reatribute articles and advices to admin or special user 'anonymous'
        // TODO: create a service to do this and an anonymous user to dump articles and advices
        $advices = $user->getAdvices();
        foreach ($advices as $advice) {
            $advice->setContributor($userRepository->find(1));
            $adviceRepository->add($advice, true);
        }
        $userRepository->remove($user, true);
        return $this->json([], Response::HTTP_NO_CONTENT, [], ['groups' => 'users']);
    }
}
