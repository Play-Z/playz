<?php

namespace App\Controller\play\announcement;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/announcement')]
class AnnouncementController extends AbstractController
{
    #[Route('/', name: 'announcement_index', methods: ['GET'])]
    public function index(AnnouncementRepository $announcementRepository): Response
    {
        return $this->render('play/announcement/index.html.twig', [
            'announcements' => $announcementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'announcement_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $announcement = new Announcement();
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($announcement);
            $entityManager->flush();

            return $this->redirectToRoute('announcement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/announcement/new.html.twig', [
            'announcement' => $announcement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'announcement_show', methods: ['GET'])]
    public function show(Announcement $announcement): Response
    {
        return $this->render('play/announcement/show.html.twig', [
            'announcement' => $announcement,
        ]);
    }

    #[Route('/{id}/edit', name: 'announcement_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Announcement $announcement): Response
    {
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('announcement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/announcement/edit.html.twig', [
            'announcement' => $announcement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'announcement_delete', methods: ['POST'])]
    public function delete(Request $request, Announcement $announcement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$announcement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($announcement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('announcement_index', [], Response::HTTP_SEE_OTHER);
    }
}
