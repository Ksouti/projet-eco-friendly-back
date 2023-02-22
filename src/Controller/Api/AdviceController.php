<?php

namespace App\Controller\Api;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AdviceController extends AbstractController
{
    // TODO : add url parameters to filter advices
    /**
     * @Route("/api/advices", name="app_api_advices_list", methods={"GET"})
     */
    public function index(AdviceRepository $adviceRepository): Response
    {
        return $this->json($adviceRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'advices']);
    }

    /**
     * @Route("/api/advices", name="app_api_advices_new", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, SluggerInterface $slugger, AdviceRepository $adviceRepository, UserRepository $userRepository, CategoryRepository $categoryRepository): Response
    {
        try {;
            $advice = $serializer->deserialize($request->getContent(), Advice::class, 'json');
            $advice->setSlug(strtolower($slugger->slug($advice->getTitle(), '-')));
            $advice->setCreatedAt(new \DateTimeImmutable());
            $json = $request->getContent();
            $contributorId = json_decode($json, true)['contributorId'];
            $advice->setContributor($userRepository->find($contributorId));
            $categoryId = json_decode($json, true)['categoryId'];
            $advice->setCategory($categoryRepository->find($categoryId));
        } catch (NotEncodableValueException $e) {
            return $this->json(['errors' => 'Json non valide'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($advice);

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

        $adviceRepository->add($advice, true);

        return $this->json(
            $advice,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_advices_read', ['id' => $advice->getId()]),
            ],
            [
                'groups' => 'advices',
            ]
        );
    }

    /**
     * @Route("/api/advices/{id}", name="app_api_advices_read", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function read(?Advice $advice, AdviceRepository $adviceRepository): Response
    {
        if (!$advice) {
            return $this->json(['errors' => 'Ce conseil n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($advice, Response::HTTP_OK, [], ['groups' => 'advices']);
    }

    /**
     * @Route("/api/advices/{id}", name="app_api_advices_update", requirements={"id":"\d+"}, methods={"PUT"})
     */
    public function update(Request $request, ?Advice $advice, SerializerInterface $serializer, ValidatorInterface $validator, SluggerInterface $slugger, AdviceRepository $adviceRepository, UserRepository $userRepository, CategoryRepository $categoryRepository): Response
    {
        if (!$advice) {
            return $this->json(['errors' => ['Conseil' => 'Ce conseil n\'existe pas']], Response::HTTP_NOT_FOUND);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $advice->setTitle($data['title'] ?? $advice->getTitle());
            $advice->setContent($data['content'] ?? $advice->getContent());
            $advice->setStatus($data['status'] ?? $advice->getStatus());
            if (isset($data['category']) && !$categoryRepository->find($data['category'])) {
                return $this->json(['errors' => ['category' => 'Cette catégorie n\'existe pas']], Response::HTTP_NOT_FOUND);
            }
            $advice->setCategory($categoryRepository->find($data['category']) ?? $advice->getCategory());
            $advice->setSlug(strtolower($slugger->slug($advice->getTitle(), '-')));
            $advice->setUpdatedAt(new \DateTimeImmutable());
        } catch (NotEncodableValueException $e) {
            return $this->json(['errors' => ['json' => 'Json non valide']], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($advice);

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

        $adviceRepository->add($advice, true);

        return $this->json(
            $advice,
            Response::HTTP_OK,
            [
                'Location' => $this->generateUrl('app_api_advices_read', ['id' => $advice->getId()]),
            ],
            [
                'groups' => 'advices',
            ]
        );
    }

    /**
     * @Route("/api/advices/{id}", name="app_api_advices_delete", requirements={"id":"\d+"}, methods={"DELETE"})
     */
    public function delete(?Advice $advice, AdviceRepository $adviceRepository): Response
    {
        if (!$advice) {
            return $this->json(['errors' => 'Ce conseil n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        $adviceRepository->remove($advice, true);
        return $this->json([], Response::HTTP_NO_CONTENT, [], ['groups' => 'advices']);
    }
}
