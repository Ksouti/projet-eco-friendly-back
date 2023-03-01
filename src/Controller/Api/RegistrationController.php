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
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    //    private EmailVerifier $emailVerifier;
    //
    //    public function __construct(EmailVerifier $emailVerifier)
    //    {
    //        $this->emailVerifier = $emailVerifier;
    //    }

    /**
     * @Route("/api/register", name="app_api_users_register", methods={"POST"})
     */
    public function register(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
            $user->setRoles(['ROLE_USER']);
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

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@eco-friendly.fr'))
            ->to($user->getEmail())
            ->subject('Confirmez votre adresse email et rejoignez-nous !')
            ->htmlTemplate('email/email_verification.html.twig')
            ->context([
                'user' => $user,
                'url' => 'http://localhost:8000/',
            ]);

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
            return $this->json(['errors' => $e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json('moui?', Response::HTTP_OK);

        //
        //        // generate a signed url and email it to the user
        //        $this->emailVerifier->sendEmailConfirmation(
        //            'app_verify_email',
        //            $user,
        //            (new TemplatedEmail())
        //                ->from(new Address('laure.riglet@gmail.com', 'L4ur3l3i\'s MailBot'))
        //                ->to($user->getEmail())
        //                ->subject('Confirmez votre adresse email et rejoignez-nous !')
        //                ->htmlTemplate('registration/confirmation_email.html.twig')
        //        );
        //        // do anything else you need here, like send an email
        //
        //        return $this->redirectToRoute('app_admin');
        //    }
        //
        //    #         return $this->render('registration/register.html.twig', [
        //    #             'registrationForm' => $form->createView(),
        //    #         ]);
        //    #     }
        //
        /**
         * @Route("/verify/email", name="app_verify_email")
         */
        //    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
        //    {
        //        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //
        //        // validate email confirmation link, sets User::isVerified=true and persists
        //        try {
        //            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        //        } catch (VerifyEmailExceptionInterface $exception) {
        //            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
        //
        //            return $this->redirectToRoute('app_register');
        //        }
        //
        //        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        //        $this->addFlash('success', 'Your email address has been verified.');
        //
        //        return $this->redirectToRoute('app_register');
        //    }
    }
}
