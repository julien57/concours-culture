<?php

namespace App\Controller\Back;

use App\Entity\Gift;
use App\Form\GiftType;
use App\Repository\GiftRepository;
use App\Service\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class GiftController extends AbstractController
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
     * @Route("/prix/liste", name="back_gift_list")
     */
    public function list(GiftRepository $giftRepository)
    {
        return $this->render('back/gift/list.html.twig', [
            'gifts' => $giftRepository->findBy([], ['id' => 'DESC'])
        ]);
    }

    /**
     * @Route("/prix/ajouter", name="back_gift_add")
     */
    public function add(Request $request, File $file)
    {
        $gift = new Gift();
        $form = $this->createForm(GiftType::class, $gift)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $documentName = $file->uploadPhotoGift($gift->getPhoto());
            $gift->setPhoto($documentName);

            $this->em->persist($gift);
            $this->em->flush();

            $this->redirectToRoute('back_gift_add');
        }

        return $this->render('back/gift/add.html.twig', [
            'form' => $form->createView(),
            'gift' => $gift
        ]);
    }

    /**
     * @Route("/prix/modifier/{id}", name="back_gift_edit")
     */
    public function edit(Gift $gift, Request $request, File $file)
    {
        $oldPhoto = $gift->getPhoto();
        $form = $this->createForm(GiftType::class, $gift)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('photo')->getData()) {
                $documentName = $file->uploadPhotoGift($gift->getPhoto());
                $gift->setPhoto($documentName);
            } else {
                $gift->setPhoto($oldPhoto);
            }

            $this->em->flush();

            $this->redirectToRoute('back_gift_add');
        }

        return $this->render('back/gift/edit.html.twig', [
            'form' => $form->createView(),
            'gift' => $gift
        ]);
    }


}
