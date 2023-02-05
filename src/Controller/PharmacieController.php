<?php

namespace App\Controller;

use App\Entity\Pharmacie;
use App\Repository\GrossisteRepository;
use App\Repository\PharmacieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
                                UrlGeneratorInterface $urlGenerator, GrossisteRepository $grossisteRepository) : JsonResponse{

        // insertion du json reçu dans la variable pharma
        $pharma = $serializer->deserialize($request->getContent(),
            Pharmacie::class,
            'json');

        // ignorer la liste initialisée par la commande précédente
        foreach ($pharma->getListeGrossistes() as $grossiste){
            $pharma->removeListeGrossiste($grossiste);
        }


        // insertion des grossistes
        $content = $request->toArray();
        $grossistes = $content['listeGrossistes'] ?? [];

        foreach ($grossistes as $grossiste){
            $gros = $grossisteRepository->find($grossiste);
            $pharma->addListeGrossiste($gros);
        }

        // enregisrer dans la BDD
        $em->persist($pharma);
        $em->flush();


        $json = $serializer->serialize($pharma, 'json');
        $location = $urlGenerator->generate('pharmacie', ['id'=> $pharma->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($json, Response::HTTP_CREATED, ["Location" => $location], true);

    }

    #[Route('/api/pharmacies/{id}', name: 'update_pharmacie', methods:['PUT'])]
    public function updatePharma(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                              Pharmacie $currentPharma ,GrossisteRepository $grossisteRepository) : JsonResponse{
        $updatePharma = $serializer->deserialize(
            $request->getContent(),
            Pharmacie::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentPharma]);


        $content = $request->toArray();
        $grossistes = $content['listeGrossistes'] ?? false;

        // supprimer les anciens grossistes si une mise à jour est demandée
        if($grossistes) {
            foreach ($updatePharma->getListeGrossistes() as $grossiste) {
                $updatePharma->removeListeGrossiste($grossiste);
            }

            foreach ($grossistes as $grossiste){
                $gros = $grossisteRepository->find($grossiste);
                $updatePharma->addListeGrossiste($gros);
            }
        }

        $em->persist($updatePharma);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/pharmacies/{id}', name: 'deletePharmacie', methods: ['DELETE'])]
    public function deletePharmacie(Pharmacie $pharmacie, EntityManagerInterface $em): JsonResponse {
        $em->remove($pharmacie);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
