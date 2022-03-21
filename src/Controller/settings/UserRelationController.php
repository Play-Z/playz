<?php

namespace App\Controller\settings;

use App\Entity\UserRelation;
use App\Repository\UserRelationRepository;
use App\Repository\UserRepository;
use App\Service\UserRelationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/relations')]
class UserRelationController extends AbstractController
{
    #[Route('/', name: 'friend_index', methods: ['GET'])]
    public function index(UserRelationRepository $userRelationRepository): Response
    {
        $user = $this->getUser();

        $userSenderRelation = $userRelationRepository->findBy(['sender' => $user]);
        $userRecipientRelation = $userRelationRepository->findBy(['recipient' => $user, 'status' => 'accepted']);

        foreach ($userSenderRelation as $i => $relation){
            if ($relation->getStatus() !== 'accepted' && $relation->getStatus() !== 'blocked'){
                unset($userSenderRelation[$i]);
            }
        }

        $userRelations = array_merge($userSenderRelation, $userRecipientRelation);


        return $this->render('settings/user_relation/index.html.twig', [
            'user_relations' => $userRelations,
        ]);
    }

    #[Route('/request', name: 'user_relation_index', methods: ['GET'])]
    public function indexRequest(UserRelationRepository $userRelationRepository): Response
    {
        $recipient = $this->getUser();

        return $this->render('settings/user_relation/index_request.html.twig', [
            'user_relations' => $userRelationRepository->findBy(['recipient' => $recipient, 'status' => 'pending']),
        ]);
    }

    #[Route('/new', name: 'user_relation_new', methods: ['POST'])]
    public function new(Request $request, UserRepository $userRepository, UserRelationService $userRelationService, UserRelationRepository $userRelationRepository): Response
    {
        $user = $this->getUser();

        $recipient = $userRepository->find($request->request->get('recipient'));
        $relationType = $request->request->get('type');

        $allBlockedUserRelation = $userRelationRepository->findBlockedRelationByUser($user, $recipient);

        if (empty($allBlockedUserRelation)){
            if ($relationType === 'team'){
                $this->addFlash('notice',
                    $userRelationService->handleNewTeamRelation($user, $recipient)
                );
            }
            elseif ($relationType === 'friend'){
                $this->addFlash('notice',
                    $userRelationService->handleNewFriendRelation($user, $recipient)
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

        return $this->redirectToRoute('friend_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}/accept', name: 'user_relation_accept', methods: ['GET'])]
    public function acceptRequest(UserRelation $userRelation, UserRelationRepository $userRelationRepository): Response
    {
        $user = $this->getUser();

        if ($userRelation->getType() === 'team'){
            $userRelationWithJoinEntity = $userRelationRepository->findUserSenderTeam(['id' => $userRelation->getId()])[0];
            if (!in_array('ROLE_ADMIN',$user->getRoles())){
                $user->setRoles((array('ROLE_TEAM_MEMBER')));
            }
            $user->setTeam($userRelationWithJoinEntity->getSender()->getTeam());
        }

        $userRelation->setStatus('accepted');
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_relation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/block', name: 'user_relation_block', methods: ['GET','POST'])]
    public function blockUser(Request $request, UserRelation $userRelation): Response
    {
        $userRelation->setStatus('blocked');
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_relation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/decline', name: 'user_relation_decline', methods: ['POST'])]
    public function declineRequest(Request $request, UserRelation $userRelation): Response
    {
        $userRelation->setStatus('rejected');
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_relation_index', [], Response::HTTP_SEE_OTHER);
    }
}
