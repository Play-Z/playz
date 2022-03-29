<?php

namespace App\Controller\play\team;

use App\Entity\Team;
use App\Entity\User;
use App\Form\EditTeamMemberType;
use App\Form\EditTeamType;
use App\Form\CreateTeamType;
use App\Repository\UserRepository;
use App\Security\Voter\TeamMemberVoter;
use App\Service\TeamService;
use App\Repository\TeamRepository;
use App\Security\Voter\TeamVoter;
use App\Service\UserRelationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team/{slug}/member')]
class TeamMemberController extends AbstractController
{
    #[Route('/', name: 'team_member_index', methods: ['GET'])]
    public function index(Team $team): Response
    {
        $members = $team->getUsers();

        return $this->render('play/team/member/index.html.twig', [
            'members' => $members,
        ]);
    }

    #[Route('/{user_slug}/edit', name: 'team_member_edit', methods: ['GET','POST'])]
    #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
    #[IsGranted(TeamMemberVoter::MANAGE, 'user')]
    public function edit(Request $request, User $user, Team $team): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(EditTeamMemberType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le membre a bien été modifier"
            );
            return $this->redirectToRoute('team_member_index', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/team/member/edit.html.twig', [
            'user' => $user,
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{user_slug}/fire', name: 'team_member_fire', methods: ['POST'])]
    #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
    #[IsGranted(TeamMemberVoter::FIRE, 'user')]
    public function fire(Request $request, User $user, Team $team, UserRelationService $userRelationService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles(['ROLE_USER']);
            $userRelationService->deleteTeamUserRelation($team, $user);
            $team->removeUser($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('team_member_index', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
    }
}