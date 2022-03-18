<?php

namespace App\Service;

class TeamService {

    public function countTeamUsers($team): bool
    {
        if (count($team->getUsers()) <= $team->getEmplacement()){
            return true;
        }
        return false;
    }

}

