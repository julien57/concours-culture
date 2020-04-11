<?php

namespace App\Controller;

use App\Form\UserInfosType;
use App\Repository\ResultRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/mon-espace")
 */
class PersonalSpace extends AbstractController
{
    /**
     * @Route("/mes-quiz", name="front_space_quiz")
     */
    public function Quiz(UserRepository $userRepository, ResultRepository $resultRepository, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $user = $userRepository->find($this->getUser()->getId());
        $quizParticiped = $resultRepository->getByUser($user);

        $form = $this->createForm(UserInfosType::class, $this->getUser())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('password')->getData()) {
                $newPassword = $form->get('password')->getData();
                $passwordEncoded = $encoder->encodePassword($user, $newPassword);
                $this->getUser()->setPassword($passwordEncoded);
            }
            $em->flush();

            if ($form->get('password')->getData()) {
                $message = (new \Swift_Message('ConcoursCulture - Nouveau mot de passe'))
                    ->setFrom('contact@concoursculture.com')
                    ->setTo($this->getUser()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'front/mail/change_pass.html.twig',
                            ['user' => $user]
                        ),
                        'text/html'
                    )
                ;

                $mailer->send($message);
            }

            $this->addFlash('notice', 'Informations modifiées.');
            return $this->redirectToRoute('front_space_quiz');
        }

        return $this->render('front/space/quiz_list.html.twig', [
            'quizs' => $quizParticiped,
            'form' => $form->createView()
        ]);
    }
}
