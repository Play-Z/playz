<?php

namespace App\Controller\play\tournament;

use App\Entity\Tournament;
use App\Entity\TournamentTeam;
use App\Entity\User;
use App\Form\InscriptionType;
use App\Form\Tournament1Type;
use App\Repository\TournamentRepository;
use App\Security\Voter\UserProfileVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournament')]
class TournamentController extends AbstractController
{
    #[Route('/', name: 'tournament_index')]
    public function index(TournamentRepository $tournamentRepository): Response
    {
        return $this->render('play/tournament/index.html.twig', [
            'tournaments'=>$tournamentRepository->findAll(),
        ]);
    }

    #[Route('/{slug}', name:'play_tournament')]
    public function showInscription(Tournament $tournament): Response
    {
       $matches = [] ;
        foreach ($tournament->getTournamentMatches() as  $match) {

            if(count($match->getMatchEnfants()->getValues() )=== 0) {
                array_push($matches,$match) ;
            }
        }
        return $this->render('play/tournament/show.html.twig',[
            'tournament' => $tournament,
            'inscription' => $tournament->getStartInscriptionAt() <= new \DateTime()  &&
                !$tournament->getStatus()  && count($tournament->getEquipes()) < $tournament->getMaxTeamParticipant()
            ,
            'equipes' => $tournament->getEquipes(),
            'matches' => $matches

        ]) ;
    }

    #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
    #[Route('/{slug}/inscription/{user_slug}', name:'play_inscription_tournament')]
    #[IsGranted(UserProfileVoter::INSCRIPTION, 'user' )]
    public function doInscription(Tournament $tournament, User $user, Request $request): Response
    {
        $tournament_team = new TournamentTeam() ;

        $isTeamAlreadyRegister = false ;
        foreach ($tournament->getEquipes() as $equipe) {
            if($equipe->getTeams()->getId() === $user->getTeam()->getId() )
                $isTeamAlreadyRegister = true ;
        }
        if(!$isTeamAlreadyRegister) {
            $form = $this->createForm(InscriptionType::class, $tournament_team, [
                'team' => $user->getTeam()->getUsers()
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {


                if (count($tournament_team->getPlayers()) > $tournament->getMaxTeamPlayers()) {
                    $this->addFlash('warning', 'Tu ne dois pas dépasser la limite de joueurs inscrit autorisé.');
                    return $this->redirectToRoute('play_inscription_tournament', [
                        'slug' => $tournament->getSlug(),
                        'user_slug' => $user->getSlug()
                    ]);
                }
                if (count($tournament->getEquipes()) + 1 <= $tournament->getMaxTeamParticipant()) {

                    $tournament_team->addTournaments($tournament);
                    $tournament_team->setTeams($user->getTeam());

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($tournament_team);
                    $em->flush();

                    $this->addFlash('success', 'Ton équipe a bien été inscrite ! ');
                    return $this->redirectToRoute('play_tournament', [
                        'slug' => $tournament->getSlug()
                    ]);
                }
                return $this->redirectToRoute('play_tournament', [
                    'slug' => $tournament->getSlug()
                ]);
            }


            return $this->render('play/tournament/register.html.twig', [
                'form' => $form->createView(),
                'team' => $user->getTeam(),
                'tournament' => $tournament
            ]);
        }
        $this->addFlash('error','Ton équipe est déjà inscrite');
        return $this->redirectToRoute('play_tournament', [
        'slug' => $tournament->getSlug()
        ]);
    }


}
