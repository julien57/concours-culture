<?php

namespace App\Controller\Api;

use App\Repository\QuizRepository;
use App\Repository\RegleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/next-concours")
     */
    public function nextEvent(QuizRepository $quizRepository)
    {
        $concoursInCurse = $quizRepository->APIConcoursInCurse();
        $nextConcours = $quizRepository->APINextConcours();

        $response = new JsonResponse();
        $response->setContent(json_encode([
            'concoursInCurse' => $concoursInCurse,
            'nextConcours' => $nextConcours
        ]));

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * @Route("/rules")
     */
    public function regles(RegleRepository $regleRepository)
    {
        $response = new JsonResponse();
        $response->setContent(json_encode([
            'regles' => $regleRepository->APIGetRegles()
        ]));

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
