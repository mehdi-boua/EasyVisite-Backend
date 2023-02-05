<?php

namespace App\Controller;

use App\Entity\Grossiste;
use App\Repository\GrossisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class GrossisteController extends AbstractController
{
    #[Route('/api/grossistes', name: 'liste_grossistes', methods: ['GET'])]
    public function listeGrossistes(GrossisteRepository $grossisteRepository, SerializerInterface $serializer): JsonResponse {

        $liste = $grossisteRepository->findAll();
        $jsonListe = $serializer->serialize($liste, 'json');
        return new JsonResponse($jsonListe, Response::HTTP_OK, [], true);
    }

    #[Route('/api/grossistes/{id}', name: 'grossiste', methods: ['GET'])]
    public function getGrossiste(Grossiste $grossiste, SerializerInterface $serializer): JsonResponse {

        $json = $serializer->serialize($grossiste, 'json');
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/grossistes', name: 'new_grossiste', methods: ['POST'])]
    public function newGrossiste(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                 UrlGeneratorInterface $urlGenerator): JsonResponse {

        $grossiste = $serializer->deserialize($request->getContent(), Grossiste::class, 'json');
        $em->persist($grossiste);
        $em->flush();

        $json = $serializer->serialize($grossiste, 'json');

        $location = $urlGenerator->generate('grossiste', ['id' => $grossiste->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/api/grossistes/{id}', name: 'update_grossiste', methods: ['PUT'])]
    public function updateGrossiste(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                 Grossiste $currentGrossite): JsonResponse {

        $updatedGrossiste = $serializer->deserialize($request->getContent(),
            Grossiste::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentGrossite]);

        $em->persist($updatedGrossiste);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/grossistes/{id}', name: 'deleteGrossiste', methods: ['DELETE'])]
    public function deleteGrossiste(Grossiste $grossiste, EntityManagerInterface $em): JsonResponse {
        $em->remove($grossiste);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
