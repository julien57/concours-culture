<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/utilisateurs/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="back_user_list")
     */
    public function list(int $page, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $userRepository->findBy([], ['id' => 'DESC']),
            $page, /*page number*/
            25 /*limit per page*/
        );

        return $this->render('back/user/user_list.html.twig', [
            'users' => $pagination
        ]);
    }

    /**
     * @Route("/utilisateurs/suppression/{id}", name="back_user_remove")
     */
    public function remove(User $user, EntityManagerInterface $em)
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('notice', 'Utilisateur supprimé !');
        return $this->redirectToRoute('back_user_list');
    }

    /**
     * @Route("/creation-admin", name="back_user_create_admin")
     */
    public function createAdmin(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(["ROLE_ADMIN"]);
            $passwordNew = uniqid();
            $passwordEncoded = $encoder->encodePassword($user, $passwordNew);
            $user->setPassword($passwordEncoded);

            $em->persist($user);
            $em->flush();

            $message = (new \Swift_Message('ConcoursCulture - Bienvenue nouvel admin !'))
                ->setFrom('contact@concoursculture.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'front/mail/new_admin.html.twig',
                        ['password' => $passwordNew, 'user' => $user]
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);

            $this->addFlash('notice', 'Administrateur créé.');
            return $this->redirectToRoute('back_user_list');
        }

        return $this->render('back/user/create_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
