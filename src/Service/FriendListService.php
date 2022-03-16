<?php

namespace App\Service;

use App\Repository\UserRelationRepository;
use Symfony\Component\Security\Core\Security;

class FriendListService
{

    public function generateUserFriendList(Security $security, UserRelationRepository $userRelationRepository)
    {
        $user = $security->getUser();
        $friendSendedUserRelation = $userRelationRepository->findBy(['sender' => $user, 'status' => 'accepted']);
        $friendRecipientUserRelation = $userRelationRepository->findBy(['sender' => $user, 'status' => 'accepted']);

        $friendRelation = array_merge($friendSendedUserRelation, $friendRecipientUserRelation);
    }
}