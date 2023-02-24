<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/back_office/connexion", name="app_backoffice_security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

<<<<<<< HEAD
<<<<<<< HEAD
     /**
=======
    /**
>>>>>>> FIX: quickfix of 2 typos
=======
    /**
>>>>>>> c3b7c9e2b232ba2b63e327a802af86ad5c732c78
     * @Route("/back_office/deconnexion", name="app_backoffice_security_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
<<<<<<< HEAD

    /**
     * @Route("/back_office/deconnexion", name="app_backoffice_security_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
=======
>>>>>>> c3b7c9e2b232ba2b63e327a802af86ad5c732c78
