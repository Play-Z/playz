<?php

namespace App\Controller\play\team;

use App\Entity\Team;
use App\Entity\User;
use App\Form\EditTeamMemberType;
use App\Form\EditTeamType;
use App\Form\CreateTeamType;
use App\Repository\UserRelationRepository;
use App\Repository\UserRepository;
use App\Service\TeamService;
use App\Repository\TeamRepository;
use App\Security\Voter\TeamVoter;
use App\Service\UserRelationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class TeamController extends AbstractController
{
    #[Route('/', name: 'team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('play/team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'team_new', methods: ['GET','POST'])]
    public function new(Request $request, UserRelationService $userRelationService, UserRelationRepository $userRelationRepository): Response
    {
        $user = $this->getUser();
        $team = new Team();

        $currentUser = $this->getUser();
        $friendsRelation = $userRelationRepository->findAllFriendsOfUser($currentUser);
        $friends = [];

        foreach ($friendsRelation as $userRelation){
            if($userRelation->getSender() !== $currentUser){
                $friends[] = $userRelation->getSender();
            }
            elseif($userRelation->getRecipient() !== $currentUser){
                $friends[] = $userRelation->getRecipient();
            }
        }

        $form = $this->createForm(CreateTeamType::class, $team, ['friends' => $friends]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recipients = $team->getUsers();

            foreach ($recipients as $recipient){
                $team->removeUser($recipient);
                $userRelationService->handleNewTeamRelation($user, $recipient);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $team->setCreatedBy($user);
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('team_show', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'team_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        $tournamentTeams = $team->getTournamentTeams()->getValues();
        $tournaments = [];

        foreach ($tournamentTeams as $tournamentTeam)
        {
            $tournaments = $tournamentTeam->getTournaments()->getValues();
        }

        return $this->render('play/team/show.html.twig', [
            'team' => $team,
            'tournaments' => $tournaments,
        ]);
    }

    #[Route('/{slug}/edit', name: 'team_edit', methods: ['GET','POST'])]
    #[IsGranted(TeamVoter::EDIT, 'team')]
    public function edit(Request $request, Team $team): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(EditTeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'success',
                "L'équipe a bien été modifier"
            );
            return $this->redirectToRoute('team_show', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/members/edit', name: 'team_members_edit', methods: ['GET','POST'])]
    #[IsGranted(TeamVoter::EDIT, 'team')]
    public function membersEdit(Request $request, Team $team, UserRepository $userRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(EditTeamMemberType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedMembers = $request->request->get('edit_team')['user'];
            $currentMembers = $userRepository->findBy(['team' => $team]);

            foreach ($currentMembers as $member){
                if (!in_array($member->getId(), $updatedMembers)){
                    $team->removeUser($member);
                }
            }
            $entityManager->flush();
            $this->addFlash(
                'success',
                "L'équipe a bien été modifier"
            );
            return $this->redirectToRoute('team_show', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'team_delete', methods: ['POST'])]
    #[IsGranted(TeamVoter::DELETE, 'team')]
    public function delete(Request $request, Team $team, UserRelationService $userRelationService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $userRelationService->deleteAllTeamUserRelation($team);
            $user = $this->getUser();
            if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_MANAGER', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_ARBITER', $user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
            }
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{slug}/join', name: 'team_join', methods: ['GET', 'POST'])]
    public function join(Team $team, TeamService $teamService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($team->getPublic() === true) {
            if ($teamService->countTeamUsers($team)){
                if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_MANAGER', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_ARBITER', $user->getRoles())){
                    $user->setRoles((array('ROLE_TEAM_MEMBER')));
                }
                $team->addUser($user);
                $entityManager->flush();
            }else{
                $this->addFlash(
                    'error',
                    "Impossible de rejoindre l'équipe car il n'y a plus de place"
                );
                return $this->redirectToRoute('team_show', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
            }
        }

        $this->addFlash(
            'success',
            "Vous avez bien rejoins l'équipe."
        );
        return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{slug}/leave', name: 'team_leave', methods: ['GET', 'POST'])]
    #[IsGranted(TeamVoter::LEAVE, 'team')]
    public function leave(Team $team, UserRelationService $userRelationService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_MANAGER', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_ARBITER', $user->getRoles())) {
            $user->setRoles(['ROLE_USER']);
        }
        $userRelationService->deleteTeamUserRelation($team, $user);
        $team->removeUser($user);
        if (count($team->getUsers()) == 0){
            $entityManager->remove($team);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Vous avez bien quitté l'équipe et elle a été supprimé."
            );
            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }
        elseif ($team->getCreatedBy() === $user){
            $members =$team->getUsers()->getValues();
            foreach ($members as $member){
                if (in_array('ROLE_TEAM_MANAGER', $member->getRoles())){
                    if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_MANAGER', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_ARBITER', $user->getRoles())) {
                        $member->setRoles(['ROLE_TEAM_CREATOR']);
                    }
                    $team->setCreatedBy($member);
                    $haveSetNewCreator = true;
                }
                else{
                    $haveSetNewCreator = false;
                }
            }

            if (!$haveSetNewCreator){
                $rand_key = array_rand($members);
                $member = $members[$rand_key];
                if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_MANAGER', $user->getRoles()) && !in_array('ROLE_TOURNAMENT_ARBITER', $user->getRoles())) {
                    $member->setRoles(['ROLE_TEAM_CREATOR']);
                }
                $team->setCreatedBy($member);
            }
        }
        $entityManager->flush();
        $this->addFlash(
            'success',
            "Vous avez bien quitté l'équipe."
        );

        return $this->redirectToRoute('team_show', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
