<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\GeneratorService;
use App\Service\SluggerService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends AbstractController

// TODO: rework this controller and the related voter to clearly separate the possible
// TODO: backoffice actions on different roles (user and author)

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
     * @Route("/back_office/utilisateurs/ajouter", name="app_backoffice_users_new", methods={"GET", "POST"})
     * @isGranted("ROLE_ADMIN", message="Accès réservé aux administrateurs")
     */
    public function new(
        Request $request,
        GeneratorService $generator,
        MailerInterface $mailer,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
    ): Response {
        $user = new User();
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setIsActive(true);
        $user->setIsVerified(false);
        $user->setCode($generator->codeGen());
        $user->setRoles(['ROLE_AUTHOR']);
        $tempPassword = $generator->passwordGen();
        $user->setPassword($tempPassword);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* $avatar = $form->get('avatar')->getData();

            if (!$avatar) {
                $this->addFlash('danger', 'Image non valide');
                // return $this->redirectToRoute('app_backoffice_users_new');
            }

            $extension = $avatar->guessExtension();
            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $this->addFlash('danger', 'Format d\'image non supporté');
                // return $this->redirectToRoute('app_backoffice_users_new');
            }

            $filename = $user->getId() . '-' . uniqid() . '.' . $extension;
            $filepath = $this->getParameter('uploads_user_directory') . '/' . $filename;

            try {
                $avatar->move(
                    $this->getParameter('uploads_user_directory'),
                    $filename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                // return $this->redirectToRoute('app_backoffice_users_new');
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
*/
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
            $userRepository->add($user, true);

            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@eco-friendly.fr', 'Eco-Friendly'))
                ->to($user->getEmail())
                ->subject('Votre compte Eco-Friendly a été créé !')
                ->htmlTemplate("email/author_account_creation.html.twig")
                ->context([
                    'username' => $user->getEmail(),
                    'password' => $tempPassword,
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('app_backoffice_authors_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back_office/utilisateurs/creation", name="app_backoffice_users_create", methods={"GET", "POST"})
     */
    public function create(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
    ): Response {
        // TODO: returns a InvalidArgumentException if the password field is not filled
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => ['Default']]);
        $password = $form->get('password')->getData();
        if ($password !== null) {
            $form->handleRequest($request);
        }

        // As author's required fields are different than these of a member (firstname and lastname mandatory), 
        // we need to check them because the @assert on the entity can't be used for this purpose (to let things open 
        // for the api registration)
        if ($form->isSubmitted()) {
            if ($user->getFirstname() == null || $user->getLastname() == null) {
                $this->addFlash('danger', 'Vous devez renseigner votre prénom et votre nom.');
            }

            if ($password == null) {
                $this->addFlash('danger', 'Vous devez renseigner un mot de passe.');
            }
            $confirmPassword = $form->get('passwordConfirm')->getData();
            if ($password != $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
            $userRepository->add($user, true);
        }
        return $this->render('user/create.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
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
        // Vérifiez si l'utilisateur à modifier a le rôle approprié
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || !in_array('ROLE_AUTHOR', $user->getRoles())) {
            throw new AccessDeniedException("Vous n'avez pas le droit de modifier cet utilisateur.");
        }

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
                    $this->addFlash('danger', 'Une erreur s\"est produite lors de l\'upload de l\'image');
                }
            }

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_backoffice_members_list', [], Response::HTTP_SEE_OTHER);
        }
        $this->addFlash(
            'success',
            'Le compte de ' . $user->getFirstname() . ' ' . $user->getLastname() . ' a bien été modifié .'
        );

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
        $this->addFlash(
            'danger',
            'Le compte de ' . $user->getFirstname() . ' ' . $user->getLastname() . ' a bien été désactivé .'
        );
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
        $this->addFlash(
            'success',
            'Le compte de ' . $user->getFirstname() . ' ' . $user->getLastname() . ' a bien été activé .'
        );
        return $this->redirectToRoute('app_backoffice_members_list', [], Response::HTTP_SEE_OTHER);
    }
}
