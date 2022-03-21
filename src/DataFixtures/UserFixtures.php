<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
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
            ->setUsername('Admin')
            ->setEmail('admin@playz.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
            ->setNewsletter(false)
            ->setLastname('Playz')
            ->setFirstname('Admin')
            ->setCountry('France')
            ->setOrganization('PlayZ')
            ->setOrganizationPosition('CEO')
            ->setDescription('Hi ! I am the CEO of playz')
        ;
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'test'));
        $manager->persist($admin);
        $this->setReference(self::USER_ADMIN, $admin);

        $player = (new User())
            ->setUsername('Player')
            ->setEmail('player@playz.com')
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(true)
            ->setNewsletter(false)
            ->setLastname('Player')
            ->setFirstname('Player')
            ->setCountry('France')
            ->setOrganization('PlayZ')
            ->setOrganizationPosition('Moderator')
            ->setDescription('Hi ! I am one of the moderator of the playz team !')
        ;
        $player->setPassword($this->userPasswordHasher->hashPassword($player, 'test'));
        $manager->persist($player);
        $this->setReference(self::USER_USER, $player);

        $player1 = (new User())
            ->setUsername('Player1')
            ->setEmail('player1@playz.com')
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(true)
            ->setNewsletter(false)
            ->setLastname('Player1')
            ->setFirstname('Player1')
            ->setCountry('France')
            ->setOrganization('PlayZ')
            ->setOrganizationPosition('Moderator')
            ->setDescription('Hi ! I am one of the moderator of the playz team !')
        ;
        $player1->setPassword($this->userPasswordHasher->hashPassword($player1, 'test'));
        $manager->persist($player1);
        $this->setReference(self::USER_USER, $player1);

        $manager->flush();
    }
}
