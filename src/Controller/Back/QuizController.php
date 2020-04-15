<?php

namespace App\Controller\Back;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Service\File;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class QuizController extends AbstractController
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
     * @Route("/quiz/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="back_quiz_list")
     */
    public function list(int $page, QuizRepository $quizRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $quizRepository->findBy([], ['atDate' => 'ASC']), /* query NOT result */
            $page, /*page number*/
            25 /*limit per page*/
        );

        return $this->render('back/quiz/list.html.twig', [
            'quizs' => $pagination
        ]);
    }

    /**
     * @Route("/quiz/ajouter", name="back_quiz_add")
     */
    public function add(Request $request, File $file)
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $documentName = $file->uploadFile($quiz->getDocument());
            if (!$documentName) {
                $this->addFlash('danger', 'Erreur lors du chargement du document.');
                return $this->redirectToRoute('back_quiz_add');
            }

            foreach ($quiz->getQuestions() as $question) {
                $quiz->addQuestion($question);
                $question->addQuiz($quiz);
            }

            $quiz->setDocument($documentName);

            $this->em->persist($quiz);
            $this->em->flush();

            return $this->redirectToRoute('back_quiz_list');
        }

        return $this->render('back/quiz/add.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz
        ]);
    }

    /**
     * @Route("/quiz/editer/{id}", name="back_quiz_edit")
     */
    public function edit(Quiz $quiz, Request $request, File $file)
    {
        $oldDocument = $quiz->getDocument();
        $form = $this->createForm(QuizType::class, $quiz)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$form->get('document')->getData()) {
                $quiz->setDocument($oldDocument);
            } else {
                $documentName = $file->uploadFile($quiz->getDocument());
                if (!$documentName) {
                    $this->addFlash('danger', 'Erreur lors du chargement du document.');
                    return $this->redirectToRoute('back_quiz_add');
                }
                $quiz->setDocument($documentName);
            }

            /** @var Question $question */
            foreach ($form->get('questions')->getData() as $question) {
               $quiz->addQuestion($question);
               $question->addQuiz($quiz);
           }

            $this->em->flush();

            return $this->redirectToRoute('back_quiz_list');
        }

        return $this->render('back/quiz/add.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz
        ]);
    }

    /**
     * @Route("/quiz/supprimer/{id}", name="back_quiz_remove")
     */
    public function remove(Quiz $quiz)
    {
        $this->em->remove($quiz);
        $this->em->flush();

        return $this->redirectToRoute('back_quiz_list');
    }
}
