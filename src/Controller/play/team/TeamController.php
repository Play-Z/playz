<?php

namespace App\Controller\play\team;

use App\Entity\Team;
use App\Form\EditTeamType;
use App\Form\TeamType;
use App\Service\TeamService;
use App\Repository\TeamRepository;
use App\Security\Voter\TeamVoter;
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
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if (!in_array('ROLE_ADMIN',$user->getRoles())){
                $user->setRoles((array('ROLE_TEAM_CREATOR')));
            }
            $team->addUser($user);
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'team_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        return $this->render('play/team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/{slug}/edit', name: 'team_edit', methods: ['GET','POST'])]
    #[IsGranted(TeamVoter::EDIT, 'team')]
    public function edit(Request $request, Team $team, TeamService $teamService): Response
    {
        $form = $this->createForm(EditTeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                "L'équipe a bien été modifier"
            );
            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('play/team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'team_delete', methods: ['POST'])]
    #[IsGranted(TeamVoter::DELETE, 'team')]
    public function delete(Request $request, Team $team): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
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
                if (!in_array('ROLE_ADMIN',$user->getRoles())){
                    $user->setRoles((array('ROLE_TEAM_MEMBER')));
                }
                $team->addUser($user);
                $entityManager->flush();
            }else{
                $this->addFlash(
                    'error',
                    "Impossible de rejoindre l'équipe car il n'y a plus de place"
                );
                return $this->redirectToRoute('team_show', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
    }
}