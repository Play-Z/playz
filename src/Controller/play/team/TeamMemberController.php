<?php

namespace App\Controller\play\team;

use App\Entity\Team;
use App\Entity\User;
use App\Form\EditTeamMemberType;
use App\Form\EditTeamType;
use App\Form\CreateTeamType;
use App\Repository\UserRepository;
use App\Service\TeamService;
use App\Repository\TeamRepository;
use App\Security\Voter\TeamVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team/{slug}/member')]
#[IsGranted(TeamVoter::EDIT, 'team')]
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
//        $entityManager = $this->getDoctrine()->getManager();
//        $form = $this->createForm(EditTeamMembersType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $updatedMembers = $request->request->get('edit_team')['users'];
//            $currentMembers = $userRepository->findBy(['team' => $team]);
//
//            foreach ($currentMembers as $member){
//                if (!in_array($member->getId(), $updatedMembers)){
//                    dd($member);
//                    $team->removeUser($member);
//                }
//            }
//            $entityManager->flush();
//            $this->addFlash(
//                'success',
//                "L'équipe a bien été modifier"
//            );
//            return $this->redirectToRoute('team_show', ['slug' => $team->getSlug()], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('play/team/edit.html.twig', [
//            'team' => $team,
//            'form' => $form,
//        ]);
    }
}
