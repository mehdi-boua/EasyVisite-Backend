<?php

namespace App\Controller;

use App\Entity\Pharmacie;
use App\Repository\PharmacieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PharmacieController extends AbstractController
{
    #[Route('/api/pharmacies', name: 'liste_pharmacies', methods:['GET'])]
    public function listePharmacies(PharmacieRepository $repository, SerializerInterface $serializer): JsonResponse{
        $liste = $repository->findAll();
        $jsonListe = $serializer->serialize($liste, 'json');

        return new JsonResponse($jsonListe, Response::HTTP_OK, [], true);
    }

    #[Route('/api/pharmacies/{id}', name: 'pharmacie', methods:['GET'])]
    public  function getPharma(Pharmacie $pharmacie, SerializerInterface $serializer){
        $json = $serializer->serialize($pharmacie, 'json');
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/pharmacies', name: 'new_pharmacie', methods:['POST'])]
    public function newPharma(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                UrlGeneratorInterface $urlGenerator) : JsonResponse{

        $pharma = $serializer->deserialize($request->getContent(),
            Pharmacie::class,
            'json');

        $em->persist($pharma);
        $em->flush();


        $json = $serializer->serialize($pharma, 'json');
        $location = $urlGenerator->generate('pharmacie', ['id'=> $pharma->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($json, Response::HTTP_CREATED, ["Location" => $location], true);

    }
}
