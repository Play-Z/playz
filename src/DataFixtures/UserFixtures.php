<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    const USER_ADMIN = 'ADMIN';
    const USER_USER = 'USER';

    /** @var UserPasswordHasherInterface $userPasswordHasher */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setEmail('admin@admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
        ;
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'test'));
        $manager->persist($admin);
        $this->setReference(self::USER_ADMIN, $admin);

        $player = (new User())
            ->setEmail('player@player')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
        ;
        $player->setPassword($this->userPasswordHasher->hashPassword($player, 'test'));
        $manager->persist($player);
        $this->setReference(self::USER_USER, $admin);

        $manager->flush();
    }
}
