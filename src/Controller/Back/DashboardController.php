<?php

namespace App\Controller\Back;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="back_dashboard")
     */
    public function index(QuizRepository $quizRepository)
    {
        $concoursInCurse = $quizRepository->concoursInCurse();
        $quizPassed = $quizRepository->getQuizPassed();
        $nextQuiz = $quizRepository->nextConcours();

        return $this->render('back/dashboard.html.twig', [
            'concoursInCurse' => $concoursInCurse,
            'quizPassed' => $quizPassed,
            'nextQuiz' => $nextQuiz
        ]);
    }
}
