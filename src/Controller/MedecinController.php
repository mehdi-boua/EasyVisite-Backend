<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Repository\MedecinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class MedecinController extends AbstractController
{
    #[Route('/api/medecins', name: 'liste_medecins', methods: ['GET'])]
    public function index(MedecinRepository $repository, SerializerInterface $serializer): JsonResponse {
        $liste = $repository->findAll();
        $json = $serializer->serialize($liste, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/medecins/{id}', name: 'medecin', methods:['GET'])]
    public  function getPharma(Medecin $medecin, SerializerInterface $serializer){
        $json = $serializer->serialize($medecin, 'json');
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/medecins', name: 'new_medecin', methods: ['POST'])]
    public function newGrossiste(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                 UrlGeneratorInterface $urlGenerator): JsonResponse {

        $medecin = $serializer->deserialize($request->getContent(), Medecin::class, 'json');
        $em->persist($medecin);
        $em->flush();

        $json = $serializer->serialize($medecin, 'json');

        $location = $urlGenerator->generate('medecin', ['id' => $medecin->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }


    #[Route('/api/medecins/{id}', name: 'update_medecin', methods: ['PUT'])]
    public function updateMedecin(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                    Medecin $currentDoc): JsonResponse {

        $updatedDoc = $serializer->deserialize($request->getContent(),
            Medecin::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentDoc]);

        $em->persist($updatedDoc);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/medecins/{id}', name: 'deleteMedecin', methods: ['DELETE'])]
    public function deleteGrossiste(Medecin $medecin, EntityManagerInterface $em): JsonResponse {
        $em->remove($medecin);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
