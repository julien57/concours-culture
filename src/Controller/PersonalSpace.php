<?php

namespace App\Controller;

use App\Form\UserInfosType;
use App\Repository\ResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mon-espace")
 */
class PersonalSpace extends AbstractController
{
    /**
     * @Route("/mes-quiz", name="front_space_quiz")
     */
    public function Quiz(ResultRepository $resultRepository, Request $request, EntityManagerInterface $em)
    {
        $quizParticiped = $resultRepository->getByUser($this->getUser());

        $form = $this->createForm(UserInfosType::class, $this->getUser())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('notice', 'Informations modifiées.');
            return $this->redirectToRoute('front_space_quiz');
        }

        return $this->render('front/space/quiz_list.html.twig', [
            'quizs' => $quizParticiped,
            'form' => $form->createView()
        ]);
    }
}
