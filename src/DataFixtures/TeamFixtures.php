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
    private static $userTeams = [
        'Why Asteroids Taste Like Bacon',
        'Life on Planet Mercury: Tan, Relaxing and Fabulous',
        'Light Speed Travel: Fountain of Youth or Fallacy',
    ];

    public function load(ObjectManager $manager): void
    {
        for ($count = 0; $count < 64; $count++) {
            $team1 = (new Team())
                ->setName("Team {$count}")
                ->setDescription("La team {$count}")
                ->setCreatedBy($this->getReference(UserFixtures::USER_USER))
                ->addUser($this->getReference(UserFixtures::USER_USER))
            ;
            $manager->persist($team1);
            $this->setReference(self::USER_ADMIN_TEAM, $team1);
        }


        $team1 = (new Team())
            ->setName('Les Admins PlayZ')
            ->setDescription('La team 1')
            ->setCreatedBy($this->getReference(UserFixtures::USER_ADMIN))
            ->addUser($this->getReference(UserFixtures::USER_ADMIN))
        ;
        $manager->persist($team1);
        $this->setReference(self::USER_ADMIN_TEAM, $team1);

        $team2 = (new Team())
            ->setName('Les Playerz')
            ->setDescription('La team 2')
            ->setCreatedBy($this->getReference(UserFixtures::USER_USER))
            ->addUser($this->getReference(UserFixtures::USER_USER))
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
