<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class MedicamentController extends AbstractController
{
    #[Route('/api/medicaments', name: 'liste_medocs', methods: ['GET'])]
    public function listeMedicaments(MedicamentRepository $medocRepository, SerializerInterface $serializer): JsonResponse {

        $liste = $medocRepository->findAll();
        $jsonListe = $serializer->serialize($liste, 'json');
        return new JsonResponse($jsonListe, Response::HTTP_OK, [], true);
    }

    #[Route('/api/medicaments/{id}', name: 'medoc', methods: ['GET'])]
    public function getMedicament(Medicament $medoc, SerializerInterface $serializer): JsonResponse {

        $json = $serializer->serialize($medoc, 'json');
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/medicaments', name: 'new_medoc', methods: ['POST'])]
    public function newMedicament(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                 UrlGeneratorInterface $urlGenerator): JsonResponse {

        $medoc = $serializer->deserialize($request->getContent(), Medicament::class, 'json');
        $em->persist($medoc);
        $em->flush();

        $json = $serializer->serialize($medoc, 'json');

        $location = $urlGenerator->generate('medoc', ['id' => $medoc->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/api/medicaments/{id}', name: 'update_medoc', methods: ['PUT'])]
    public function updateMedicament(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                    Medicament $currentGrossite): JsonResponse {

        $updatedMedicament = $serializer->deserialize($request->getContent(),
            Medicament::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentGrossite]);

        $em->persist($updatedMedicament);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/medicaments/{id}', name: 'deleteMedicament', methods: ['DELETE'])]
    public function deleteMedicament(Medicament $medoc, EntityManagerInterface $em): JsonResponse {
        $em->remove($medoc);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
