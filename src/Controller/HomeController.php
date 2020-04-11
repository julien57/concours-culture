<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Form\ConcoursType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\RegleRepository;
use App\Repository\ResponseRepository;
use App\Repository\ResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(QuizRepository $quizRepository)
    {
        $themes = $quizRepository->getByTheme();
        $nextConcours = $quizRepository->nextConcoursWithGift();

        return $this->render('front/index.html.twig', [
            'controller_name' => 'HomeController',
            'themes' => $themes,
            'nextConcours' => $nextConcours
        ]);
    }

    /**
     * @Route("/prochain-concours", name="front_next_concours")
     */
    public function prochainConcours(QuizRepository $quizRepository, QuestionRepository $questionRepository, RegleRepository $regleRepository, ResultRepository $resultRepository)
    {
        $concoursInCurse = $quizRepository->concoursInCurse();
        $nextConcours = $quizRepository->nextConcours();

        if ($concoursInCurse !== null) {
            $questions = $questionRepository->getByQuiz($concoursInCurse);
            $arrayUserPlayed = [];
            foreach ($questions as $question) {
                $results = $resultRepository->findBy(['question' => $question, 'user' => $this->getUser()]);
                if (!empty($results)) {
                    $arrayUserPlayed[] = $results;
                }
            }
        } else {
            $arrayUserPlayed = [];
        }

        if (!empty($arrayUserPlayed)) {
            $userPlayed = true;
        } else {
            $userPlayed = false;
        }

        return $this->render('front/next_concours.html.twig', [
            'concoursInCurse' => $concoursInCurse,
            'nextConcours' => $nextConcours,
            'regles' => $regleRepository->findAll(),
            'userPlayed' => $userPlayed
        ]);
    }

    /**
     * @Route("/quiz/start/{id}", name="front_start_quiz")
     */
    public function startQuiz(Quiz $quiz, QuestionRepository $questionRepository, Request $request, SessionInterface $session, ResponseRepository $responseRepository)
    {
        if ($request->get('concours')['question'] && $request->isMethod('POST')) {

            $dateEnd = new \DateTime('', new \DateTimeZone('Europe/Paris'));

            $sessionQuestions = $session->get('questions');

            $idQuestion = array_shift($sessionQuestions);

            $session->set('questions', $sessionQuestions);

            $questionAnswer = $questionRepository->find($idQuestion);
            if (!$questionAnswer) {
                return $this->redirectToRoute('front_next_concours');
            }

            $response = $responseRepository->findOneBy(['question' => $questionAnswer, 'response' => $request->get('concours')['question']]);
            if (!$response) {
                return $this->redirectToRoute('front_next_concours');
            }

            $result = new Result();
            $result->setQuestion($questionAnswer);
            $result->setResponse($response);
            $result->setUser($this->getUser());
            $result->setQuiz($quiz);
            $result->setStartAt(new \DateTime($session->get('quizStartAt'), new \DateTimeZone('Europe/Paris')));
            $result->setEndAt($dateEnd);

            $this->em->persist($result);
            $this->em->flush();

            if (empty($sessionQuestions)) {
                return $this->redirectToRoute('front_quiz_success');
            }

            $nextQuestion = $questionRepository->find($session->get('questions')[0]);
            if (!$nextQuestion) {
                return $this->redirectToRoute('home');
            }

            $session->remove('quizStartAt');
            $session->remove('quizEndAt');
            $form = $this->createForm(ConcoursType::class, $nextQuestion)->handleRequest($request);

        } else {

            $questions = $questionRepository->getByQuiz($quiz);
            $arrayQuestions = [];
            foreach ($questions as $key => $question) {
                $arrayQuestions[$key] = $question->getId();
            }

            $session->set('quizId', $quiz->getId());
            $session->set('questions', $arrayQuestions);

            $form = $this->createForm(ConcoursType::class, $questions[0])
                ->handleRequest($request);
        }

        $dateNow = new \DateTime('', new \DateTimeZone('Europe/Paris'));
        $session->set('quizStartAt', $dateNow->format('H:i:s'));
        return $this->render('front/start_concours.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/quiz/termine", name="front_quiz_success")
     */
    public function quizSuccess(SessionInterface $session, QuizRepository $quizRepository)
    {
        if (!$session->get('quizStartAt') || $session->get('questions') || !$this->getUser()) {
            return $this->redirectToRoute('front_next_concours');
        }

        $quiz = $quizRepository->find($session->get('quizId'));
        $session->remove('questions');
        $session->remove('quizId');

        return $this->render('front/success.html.twig', [
            'quiz' => $quiz
        ]);
    }
}
