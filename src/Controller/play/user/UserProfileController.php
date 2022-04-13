<?php

namespace App\Controller\play\user;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use App\Security\Voter\UserProfileVoter;
use App\Service\ScoreService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserProfileController extends AbstractController
{
    #[Route('/', name: 'user_profile_index', methods: ['GET'])]
    public function index(UserRepository $userRepository)
    {
        return $this->render('play/user/index.html.twig', [
            'users' => $userRepository->findBy(['isClosed' => false]),
        ]);
    }

    #[Route('/{slug}', name: 'user_profile_show', methods: ['GET'])]
    public function show(User $user, ScoreService $scoreService): Response
    {
        if ($user->getIsClosed() !== true){

            $tournamentsTeams = $user->getTournamentTeams()->getValues();
            $tournaments = [];

            foreach ($tournamentsTeams as $tournamentsTeam){
                $tournaments = $tournamentsTeam->getTournaments()->getValues();
            }

            return $this->render('play/user/show.html.twig', [
                'tournaments' => $tournaments,
                'user' => $user,
                'ratio' => $scoreService->getPlayerOrTeamRatio($user)
            ]);
        }
        else{
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
    }

    #[IsGranted(UserProfileVoter::EDIT, 'user')]
    #[Route('/{slug}/edit', name: 'user_profile_edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre profil a bien été modifier !');
            return $this->redirectToRoute('user_profile_show', ['slug' => $user->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
