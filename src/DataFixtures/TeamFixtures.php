<?php

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture implements DependentFixtureInterface
{
    const USER_ADMIN_TEAM = 'TEAM1';
    const USER_USER_TEAM = 'TEAM2';

    public function load(ObjectManager $manager): void
    {
        $team1 = (new Team())
            ->setName('Team1')
            ->setDescription('La team 1')
            ->setCreatedBy($this->getReference(UserFixtures::USER_ADMIN))
            ->addMember($this->getReference(UserFixtures::USER_ADMIN))
        ;
        $manager->persist($team1);
        $this->setReference(self::USER_ADMIN_TEAM, $team1);

        $team2 = (new Team())
            ->setName('Team2')
            ->setDescription('La team 2')
            ->setCreatedBy($this->getReference(UserFixtures::USER_USER))
            ->addMember($this->getReference(UserFixtures::USER_USER))
        ;
        $manager->persist($team2);
        $this->setReference(self::USER_USER_TEAM, $team2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
