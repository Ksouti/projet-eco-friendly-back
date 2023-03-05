<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiAccessDeniedSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {

        // Je récupère l'exception levé par l'application
        // I get the exception raised by the application
        $exception = $event->getThrowable();

        // Si mon exception n'est pas en lien avec le controle d'accès, je ne fais rien en retournant tout de suite la méthode
        // If my exception is not related to access control, I do nothing by immediately returning the method
        if (!$exception instanceof AccessDeniedHttpException) {
            return;
        }

        // on récupère la requete
        // we retrieve the request
        $request = $event->getRequest();

        // Je récupère la route
        // we retrieve the route
        $route = $request->get("_route");

        // Si api n'est pas trouvé dans la string $route, on return pour stopper la fonction
        // If api is not found in the string $route, we "return" to stop the function
        if (!strpos($route, "api")) {
            return;
        }

        // Je crée ma réponse http en json
        // I create my http response in json
        $response = new JsonResponse(["errors" => ["authorization" => ["Vous n'avez pas les droits d'accès"]]], Response::HTTP_FORBIDDEN);
        // Je modifie la réponse avec l'erreur symfo par ma réponse personnalisé c'est à dire un status forbidden et mon json
        // I modify the response with the symfo error by my personalized response, that's to say a forbidden status and my json

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {



        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
