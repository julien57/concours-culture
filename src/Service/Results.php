<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\QuizRepository;
use App\Repository\ResultRepository;
use App\Repository\UserRepository;

class Results
{
    /**
     * @var ResultRepository
     */
    private $resultRepository;
    /**
     * @var QuizRepository
     */
    private $quizRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(ResultRepository $resultRepository, QuizRepository $quizRepository, UserRepository $userRepository)
    {
        $this->resultRepository = $resultRepository;
        $this->quizRepository = $quizRepository;
        $this->userRepository = $userRepository;
    }

    public function getTimeQuizWithCountResponses(User $user, int $quizId)
    {
        $quiz = $this->quizRepository->find($quizId);
        $results = $this->resultRepository->findBy(['quiz' => $quiz, 'user' => $user]);

        $firstResult = $results[0];
        $endResult = end($results);

        $interval = $firstResult->getStartAt()->diff($endResult->getEndAt());

        $time = $interval->h.'h'.$interval->i.'m'.$interval->s.'s';

        return [
            'time' => $time,
            'nbResponses' => count($results)
        ];
    }

    public function getGoodResults(User $user, int $quizId)
    {
        $quiz = $this->quizRepository->find($quizId);
        $results = $this->resultRepository->getGoodResultsByQuiz($user, $quiz);

        return count($results);
    }

    public function positionUserPerQuiz(int $quizId)
    {
        $quiz = $this->quizRepository->find($quizId);
        $users = $this->userRepository->getUserPerQuiz($quiz);

        return $users;
    }

    public function quizInCurse()
    {
        $quizInCurse = $this->quizRepository->concoursInCurse();
        if ($quizInCurse) {
            return ['quizInCurse' => $quizInCurse->getAtFinish()];
        } else {
            return null;
        }
    }

    public function nextQuiz()
    {
        $nextQuiz = $this->quizRepository->nextConcours();
        if ($nextQuiz) {
            return ['nextQuiz' => $nextQuiz->getAtDate()];
        } else {
            return null;
        }
    }
}
