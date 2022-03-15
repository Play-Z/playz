<?php

namespace App\Controller\settings;

use App\Entity\User;
use App\Entity\UserRelation;
use App\Form\UserRelationType;
use App\Repository\UserRelationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/friends')]
class UserRelationController extends AbstractController
{
    #[Route('/', name: 'user_relation_index', methods: ['GET'])]
    public function index(UserRelationRepository $userRelationRepository): Response
    {
        return $this->render('settings/user_relation/index.html.twig', [
            'user_relations' => $userRelationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_relation_new', methods: ['POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $sender = $this->getUser();
        $recipient = $userRepository->find($request->request->get('recipient'));

        $userRelation = (new UserRelation())
            ->addSender($sender)
            ->addRecipient($recipient);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($userRelation);
        $entityManager->flush();

        return $this->redirectToRoute('user_profile_show', ['slug' => $recipient->getSlug()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'user_relation_show', methods: ['GET'])]
    public function show(UserRelation $userRelation): Response
    {
        return $this->render('settings/user_relation/show.html.twig', [
            'user_relation' => $userRelation,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_relation_edit', methods: ['GET','POST'])]
    public function edit(Request $request, UserRelation $userRelation): Response
    {
        $form = $this->createForm(UserRelationType::class, $userRelation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_relation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/user_relation/edit.html.twig', [
            'user_relation' => $userRelation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_relation_delete', methods: ['POST'])]
    public function delete(Request $request, UserRelation $userRelation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userRelation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userRelation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_relation_index', [], Response::HTTP_SEE_OTHER);
    }
}
