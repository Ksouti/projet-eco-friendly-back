<?php

namespace App\Controller\Api;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\SluggerService;
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
    /**
     * @Route("/api/advices", name="app_api_advices_list", methods={"GET"})
     */
    public function list(Request $request, AdviceRepository $adviceRepository): Response
    {
        $category = $request->get('category', null);
        $status = $request->get('status', null);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', ($page - 1) * $limit ?? 0);
        $sortType = $request->get('sorttype', 'created_at');
        $order = $request->get('order', 'desc');
        $search = $request->get('search', null);

        return $this->json(
            $adviceRepository->findAllWithParameters($category, $status, $limit, $offset, $sortType, $order, $search),
            Response::HTTP_OK,
            [],
            ['groups' => 'advices']
        );
        return $this->json($adviceRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'advices']);
    }

    /**
     * @Route("/api/advices", name="app_api_advices_new", methods={"POST"})
     */
    public function new(Request $request, SluggerService $slugger, SerializerInterface $serializer, ValidatorInterface $validator, AdviceRepository $adviceRepository): Response
    {
        try {
            $advice = $serializer->deserialize($request->getContent(), Advice::class, 'json');
            $advice->setSlug($slugger->slugify($advice->getTitle()));
            $advice->setCreatedAt(new \DateTimeImmutable());
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

    // TODO: refine this method taking into account that we now have a denormalizer
    /**
     * @Route("/api/advices/{id}", name="app_api_advices_update", requirements={"id":"\d+"}, methods={"PUT"})
     */
    public function update(Request $request, ?Advice $advice, SluggerService $slugger, SerializerInterface $serializer, ValidatorInterface $validator, AdviceRepository $adviceRepository, CategoryRepository $categoryRepository): Response
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
            $advice->setContributor($data['contributor'] ?? $advice->getContributor());
            $advice->setCategory($categoryRepository->find($data['category']) ?? $advice->getCategory());
            $advice->setSlug($slugger->slugify($advice->getTitle()));
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
