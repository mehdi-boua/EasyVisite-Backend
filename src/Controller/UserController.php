<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/api/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, UserRepository $repository, SerializerInterface $serializer): JsonResponse {
        $content = $request->toArray();

        $db = $repository->findOneBy(['mail' => $content['mail'] ]);
        if($db == null || $db->getMdp() != $content['mdp'])
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED, [], true);

        $json = $serializer->serialize($db, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
