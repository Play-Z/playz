<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture
{

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(User::class, 10, function (User $user, $count) use ($manager) {
            $article->setTitle($this->faker->randomElement(self::$articleTitles))
            // publish most articles
            if ($this->faker->boolean(70)) {
                $article->setPublishedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            }
            $article->setAuthor($this->faker->randomElement(self::$articleAuthors))
                ->setHeartCount($this->faker->numberBetween(5, 100))
                ->setImageFilename($this->faker->randomElement(self::$articleImages));
            $comment1 = new Comment();
            $comment1->setAuthorName('Mike Ferengi');
            $comment1->setContent('I ate a normal rock once. It did NOT taste like bacon!');
            $manager->persist($comment1);
        });
        $manager->flush();
    }

//    const USER_ADMIN = 'ADMIN';
//    const USER_USER = 'USER';
//
//    /** @var UserPasswordHasherInterface $userPasswordHasher */
//    private $userPasswordHasher;
//
//    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
//        $this->userPasswordHasher = $userPasswordHasher;
//    }
//
//    public function load(ObjectManager $manager): void
//    {
//        $faker = \Faker\Factory::create('fr_FR');
//
//
//        for ($count = 0; $count < 10; $count++) {
//            $user = (new User())
//                ->setUsername($faker->userName)
//                ->setEmail($faker->email)
//                ->setRoles(['ROLE_USER'])
//                ->setIsVerified(true)
//                ->setNewsletter(false)
//                ->setLastname($faker->lastName)
//                ->setFirstname($faker->firstName)
//                ->setCountry($faker->country)
//            ;
//            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'test'));
//            $manager->persist($user);
//            $this->setReference(self::USER_USER, $user);
//        }
//
//        $admin = (new User())
//            ->setUsername('Admin')
//            ->setEmail('admin@playz.com')
//            ->setRoles(['ROLE_ADMIN'])
//            ->setIsVerified(true)
//            ->setNewsletter(false)
//            ->setLastname('Playz')
//            ->setFirstname('Admin')
//            ->setCountry('France')
//            ->setOrganization('PlayZ')
//            ->setOrganizationPosition('CEO')
//            ->setDescription('Hi ! I am the CEO of playz')
//        ;
//        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'test'));
//        $manager->persist($admin);
//        $this->setReference(self::USER_ADMIN, $admin);
//
//        $player = (new User())
//            ->setUsername('Player')
//            ->setEmail('player@playz.com')
//            ->setRoles(['ROLE_USER'])
//            ->setIsVerified(true)
//            ->setNewsletter(false)
//            ->setLastname('Player')
//            ->setFirstname('Player')
//            ->setCountry('France')
//            ->setOrganization('PlayZ')
//            ->setOrganizationPosition('Moderator')
//            ->setDescription('Hi ! I am one of the moderator of the playz team !')
//        ;
//        $player->setPassword($this->userPasswordHasher->hashPassword($player, 'test'));
//        $manager->persist($player);
//        $this->setReference(self::USER_USER, $player);
//
//        $manager->flush();
//    }
}
