<?php

namespace App\Controller\Back;

use App\Entity\Question;
use App\Entity\Response;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin")
 */
class QuestionController extends AbstractController
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
     * @Route("/questions/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="back_questions_list")
     */
    public function list(int $page, QuestionRepository $questionRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $questionRepository->findBy([], ['id' => 'DESC']), /* query NOT result */
            $page, /*page number*/
            25 /*limit per page*/
        );

        return $this->render('back/questions/list.html.twig', [
            'questions' => $pagination
        ]);
    }

    /**
     * @Route("/questions/ajouter", name="back_questions_add")
     */
    public function add(Request $request, SluggerInterface $slugger)
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($question->getResponses() as $response) {
                $response->setQuestion($question);
                $response->setSlug($slugger->slug($response->getResponse()));
            }

            $question->setSlug($slugger->slug($question->getQuestion()));

            $this->em->persist($question);
            $this->em->flush();

            return $this->redirectToRoute('back_questions_list');
        }

        return $this->render('back/questions/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/questions/editer/{id}", name="back_questions_edit")
     */
    public function edit(Question $question, Request $request, SluggerInterface $slugger)
    {
        $form = $this->createForm(QuestionType::class, $question)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            foreach ($question->getResponses() as $response) {
                $response->setQuestion($question);
                $response->setSlug($slugger->slug($response->getResponse()));
            }

            $question->setSlug($slugger->slug($question->getQuestion()));

            $this->em->persist($question);
            $this->em->flush();

            return $this->redirectToRoute('back_questions_list');
        }

        return $this->render('back/questions/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/questions/suppression/{id}", name="back_questions_remove")
     */
    public function remove(Question $question)
    {
        $this->em->remove($question);
        $this->em->flush();

        return $this->redirectToRoute('back_questions_list');
    }

    /**
     * @Route("/questions/suppression-reponse/{id}", name="back_questions_remove_response")
     */
    public function removeResponse(Response $response)
    {
        $question = $response->getQuestion();
        $this->em->remove($response);
        $this->em->flush();

        return $this->redirectToRoute('back_questions_edit', ['id' => $question->getId()]);
    }
}
