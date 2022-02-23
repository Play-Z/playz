<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\Voter\UserProfileVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/profile')]
class UserProfileController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/', name: 'connected_user_profile_show', methods: ['GET'])]
    public function showConnected()
    {
        /** @var User $user */
        $user = $this->getUser();


        return $this->redirectToRoute('user_profile_show', ['slug' => $user->getSlug()]);
    }

    #[Route('/{slug}', name: 'user_profile_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted(UserProfileVoter::EDIT, 'user')]
    #[Route('/{slug}/edit', name: 'user_profile_edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
