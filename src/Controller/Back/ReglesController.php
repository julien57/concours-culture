<?php

namespace App\Controller\Back;

use App\Entity\Regle;
use App\Form\RegleType;
use App\Repository\RegleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class ReglesController extends AbstractController
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
     * @Route("/regles", name="back_regles_list")
     */
    public function list(RegleRepository $regleRepository)
    {
        return $this->render('back/regle/list.html.twig', [
            'regles' => $regleRepository->findBy([], ['id' => 'ASC'])
        ]);
    }

    /**
     * @Route("/regles/ajouter", name="back_regles_add")
     */
    public function add(Request $request)
    {
        $regle = new Regle();
        $form = $this->createForm(RegleType::class, $regle)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($regle);
            $this->em->flush();

            return $this->redirectToRoute('back_regles_list');
        }

        return $this->render('back/regle/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/regles/editer/{id}", name="back_regles_edit")
     */
    public function edit(Regle $regle, Request $request)
    {
        $form = $this->createForm(RegleType::class, $regle)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            return $this->redirectToRoute('back_regles_list');
        }

        return $this->render('back/regle/edit.html.twig', [
            'form' => $form->createView(),
            'regle' => $regle
        ]);
    }

    /**
     * @Route("/regles/supprimer/{id}", name="back_regles_remove")
     */
    public function remove(Regle $regle)
    {
        $this->em->remove($regle);

        $this->em->flush();

        return $this->redirectToRoute('back_regles_list');
    }
}
