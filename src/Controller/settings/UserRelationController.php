<?php

namespace App\Controller\settings;

use App\Entity\User;
use App\Entity\UserRelation;
use App\Repository\UserRelationRepository;
use App\Service\UserRelationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/relations')]
class UserRelationController extends AbstractController
{
    #[Route('/', name: 'user_relation_index', methods: ['GET'])]
    public function index(UserRelationRepository $userRelationRepository): Response
    {
        $user = $this->getUser();
        $userRelations = $userRelationRepository->findAllFriendUserRelation($user);

        return $this->render('settings/user_relation/index.html.twig', [
            'user_relations' => $userRelations,
        ]);
    }

    #[Route('/request', name: 'user_relation_request_index', methods: ['GET'])]
    public function indexRequest(UserRelationRepository $userRelationRepository): Response
    {
        $recipient = $this->getUser();

        $userRelationRequest = $userRelationRepository->findBy(['recipient' => $recipient, 'status' => 'pending']);

        return $this->render('settings/user_relation/index_request.html.twig', [
            'user_relations' => $userRelationRequest,
        ]);
    }

    #[Route('/new/{user_slug}', name: 'user_relation_new', methods: ['POST'])]
    #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
    public function new(Request $request, User $user, UserRelationService $userRelationService, UserRelationRepository $userRelationRepository): Response
    {
        $sender = $this->getUser();

        $recipient = $user;
        $relationType = $request->request->get('type');

        $allBlockedUserRelation = $userRelationRepository->findBlockedRelationByUser($sender, $recipient);

        if (empty($allBlockedUserRelation)){
            if ($relationType === 'team'){
                $this->addFlash('notice',
                    $userRelationService->handleNewTeamRelation($sender, $recipient)
                );
            }
            elseif ($relationType === 'friend'){
                $this->addFlash('notice',
                    $userRelationService->handleNewFriendRelation($sender, $recipient)
                );
            }
        }
        else{
            $this->addFlash('notice',
                "Cet utilisateur vous a bloquer vous ne pouvez pas intéragir avec lui."
            );
        }

        return $this->redirectToRoute('user_profile_show', ['slug' => $recipient->getSlug()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'user_relation_delete', methods: ['POST'])]
    public function delete(Request $request, UserRelation $userRelation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userRelation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userRelation);
            $entityManager->flush();

            $this->addFlash('success',
                "Votre relation avec cet utilisateur a été supprimer"
            );
        }

        return $this->redirectToRoute('user_relation_request_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}/accept', name: 'user_relation_accept', methods: ['GET'])]
    public function acceptRequest(UserRelation $userRelation, UserRelationRepository $userRelationRepository): Response
    {
        $user = $this->getUser();

        if ($userRelation->getType() === 'team'){
            $userRelationWithJoinEntity = $userRelationRepository->findUserRelationById(['id' => $userRelation->getId()])[0];
            if (!in_array('ROLE_ADMIN',$user->getRoles())){
                $user->setRoles((array('ROLE_TEAM_MEMBER')));
            }
            $user->setTeam($userRelationWithJoinEntity->getSender()->getTeam());
        }

        $userRelation->setStatus('accepted');
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_relation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/block/{user_slug}', name: 'user_relation_block', methods: ['GET', 'POST'])]
    #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
    public function blockUser(UserRelationService $userRelationService, User $user): Response
    {
        $this->addFlash('success',
            $userRelationService->handleBlock($user)
        );

        return $this->redirectToRoute('user_profile_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/decline', name: 'user_relation_decline', methods: ['POST'])]
    public function declineRequest(UserRelation $userRelation): Response
    {
        $userRelation->setStatus('rejected');
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_relation_index', [], Response::HTTP_SEE_OTHER);
    }
}
