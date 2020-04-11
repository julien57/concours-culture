<?php

namespace App\Controller\Back;

use App\Entity\Quiz;
use App\Service\Results;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class ParticipantsController extends AbstractController
{
    /**
     * @Route("/participants/{id}", name="back_participants")
     */
    public function listParticipants(Quiz $quiz, Results $results)
    {
        $users = $results->positionUserPerQuiz($quiz->getId());

        return $this->render('back/participants/classement_quiz.html.twig', [
            'users' => $users,
            'quiz' => $quiz
        ]);
    }
}
