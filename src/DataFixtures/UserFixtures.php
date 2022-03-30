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
    const USER_TOURNAMENT_MANAGER = 'TOURNAMENT_MANAGER';
    const USER_ARBITER = 'ARBITER';

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
            ->setDescription('Hi ! I am the CEO of playz')
            ->setRedditUsername('playz')
            ->setTwitchUsername('playz')
            ->setTwitterUsername('playz')
            ->setDiscordServerToken('aWZEtUHvp4')
            ->setYoutubeUsername('playzrtve')
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
            ->setDescription('Hi ! I am one of the moderator of the playz team !')
            ->setRedditUsername('playz')
            ->setTwitchUsername('playz')
            ->setTwitterUsername('playz')
            ->setDiscordServerToken('aWZEtUHvp4')
            ->setYoutubeUsername('playzrtve')
        ;
        $player->setPassword($this->userPasswordHasher->hashPassword($player, 'test'));
        $manager->persist($player);
        $this->setReference(self::USER_USER, $player);

        $tournamentManager = (new User())
            ->setUsername('Manager')
            ->setEmail('tournament@playz.com')
            ->setRoles(['ROLE_TOURNAMENT_MANAGER'])
            ->setIsVerified(true)
            ->setNewsletter(false)
            ->setLastname('Manager')
            ->setFirstname('Tournament')
            ->setCountry('France')
            ->setDescription('Hi ! I am one of the tournament manager of the playz team !')
            ->setRedditUsername('playz')
            ->setTwitchUsername('playz')
            ->setTwitterUsername('playz')
            ->setDiscordServerToken('aWZEtUHvp4')
            ->setYoutubeUsername('playzrtve')
        ;
        $tournamentManager->setPassword($this->userPasswordHasher->hashPassword($tournamentManager, 'test'));
        $manager->persist($tournamentManager);
        $this->setReference(self::USER_TOURNAMENT_MANAGER, $tournamentManager);

        $manager->flush();

        $arbiter = (new User())
            ->setUsername('Arbiter')
            ->setEmail('arbiter@playz.com')
            ->setRoles(['ROLE_TOURNAMENT_ARBITER'])
            ->setIsVerified(true)
            ->setNewsletter(false)
            ->setLastname('Arbiter')
            ->setFirstname('Tournament')
            ->setCountry('France')
            ->setDescription('Hi ! I am one of the tournament manager of the playz team !')
            ->setRedditUsername('playz')
            ->setTwitchUsername('playz')
            ->setTwitterUsername('playz')
            ->setDiscordServerToken('aWZEtUHvp4')
            ->setYoutubeUsername('playzrtve')
        ;
        $arbiter->setPassword($this->userPasswordHasher->hashPassword($arbiter, 'test'));
        $manager->persist($arbiter);
        $this->setReference(self::USER_ARBITER, $arbiter);

        $manager->flush();
    }
}
