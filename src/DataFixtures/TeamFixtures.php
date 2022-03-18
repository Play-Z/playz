<?php

namespace App\DataFixtures;

use App\Factory\TeamFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TeamFactory::createMany(6, ['users' => UserFactory::new()->many(1, 5)]);
        $manager->flush();
    }
}
