<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/api/register", name="app_api_users_register", methods={"POST"})
     */
    public function register(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
            $user->setRoles(['ROLE_USER']);
            $user->setIsActive(true);
            $user->setIsVerified(false);
            $user->setCreatedAt(new DateTimeImmutable());
        } catch (NotEncodableValueException $e) {
            return $this->json(['errors' => 'Json non valide'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorsArray], Response::HTTP_BAD_REQUEST);
        }
        $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
        $userRepository->add($user, true);

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@eco-friendly.fr', 'Eco-Friendly'))
                ->to($user->getEmail())
                ->subject('Confirmez votre adresse email et rejoignez-nous !')
                ->htmlTemplate('email/email_verification.html.twig')
        );
        // do anything else you need here, like send an email
        return $this->json($userRepository->find($user->getId()), Response::HTTP_OK, [], ['groups' => 'users']);
    }
    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (!$id) {
            return $this->redirectToRoute('app_root');
        }

        $user = $userRepository->find($id);

        if (!$user) {
            return $this->redirectToRoute('app_root');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->json(['errors' => $exception->getReason()], Response::HTTP_BAD_REQUEST);
        }
        return $this->json($userRepository->find($request->getUser()), Response::HTTP_OK, [], ['groups' => 'users']);
    }
}
