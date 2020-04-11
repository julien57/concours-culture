<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/connexion-inscription", name="front_security_login")
     */
    public function subscriptionLogin(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user,['csrf_protection' => false])->handleRequest($request);

        $formForgot = $this->createForm(ForgotPasswordType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(["ROLE_USER"]);
            $passwordEncoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordEncoded);

            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Merci pour votre inscription, vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('front_security_login');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/forgot-password", name="front_forgot_password")
     */
    public function forgotPassword(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        if (!$request->get('forgot')) {
            return $this->redirectToRoute('front_security_login');
        }

        $tokenCsrf = $request->request->get('token');
        if ($this->isCsrfTokenValid('token', $tokenCsrf)) {
            return $this->json([
                'message' => 'Token CSRF non valide',
                'color' => 'red'
            ]);
        }

        $user = $userRepository->findOneBy(['email' => $request->get('forgot')]);

        if (!$user) {
            return $this->json([
                'message' => 'Aucun utilisateur trouvé avec cette adresse mail',
                'color' => 'red'
            ]);
        }

        $newPassword = uniqid();
        $passwordEncoded = $encoder->encodePassword($user, $newPassword);
        $user->setPassword($passwordEncoded);

        $em->flush();

        $message = (new \Swift_Message('ConcoursCulture - Nouveau mot de passe'))
            ->setFrom('contact@concoursculture.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'front/mail/forgot_password.html.twig',
                    ['password' => $newPassword, 'user' => $user]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $this->json([
            'message' => 'Nouveau mot de passe envoyé par mail',
            'color' => 'green'
        ]);
    }

    /**
     * @Route("/logout", name="front_security_logout")
     */
    public function logout()
    {
    }
}
