<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {

            $object = (new Game())
                ->setName($faker->word)
                ->setDescription($faker->word)
                ->setLogo($faker->imageUrl($width = 640, $height = 480, 'cats'));

            $manager->persist($object);
        }

        $manager->flush();
    }
}
